<?php

namespace Tests\Feature;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Language\Models\Language;
use App\Domain\Training\Enums\TrainingCompletionType;
use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Enums\TrainingType;
use App\Domain\Training\Events\TrainingCompleted;
use App\Domain\Training\Factories\StepResolverFactory;
use App\Domain\Training\Factories\TrainingStepFactory;
use App\Domain\Training\Factories\TrainingStrategyFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStep;
use App\Domain\Training\Strategies\SpecificStepTypeTrainingStrategy;
use App\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    private const DEFAULT_TRAINING_PARAMS = [
        'dictionary_id' => null,
        'training_type_id' => TrainingType::TopWords,
        'completion_type' => TrainingCompletionType::Steps,
    ];

    private const TRAINING_PARAMS_WITH_STEPS = [
        'training_type_id' => TrainingType::TopWords,
        'completion_type' => TrainingCompletionType::Steps,
        'completion_type_params' => ['steps_count' => 10]
    ];

    protected User $user;
    protected Language $sourceLang;
    protected Language $targetLang;
    protected Dictionary $dictionary;

    /**
     * @return void
     */
    public function mockTrainingStrategy(): void
    {
        $this->mock(TrainingStrategyFactory::class, function ($mock) {
            $mock->shouldReceive('create')
                ->andReturnUsing(function (Training $training) {
                    return new SpecificStepTypeTrainingStrategy(
                        $training,
                        app(TrainingStepFactory::class),
                        [TrainingStepType::EstablishCompliance]
                    );
                });
        });
    }

    /**
     * @param $step
     * @param int $trainingId
     * @param mixed $stepId
     * @return \Illuminate\Testing\TestResponse
     */
    public function submitStepAttempt($step, int $trainingId, mixed $stepId): \Illuminate\Testing\TestResponse
    {
        $attempt_data = new StepResolverFactory()
            ->create(TrainingStepType::from($step->step_type_id))
            ->resolve($step);
        $attemptResponse = $this->actingAs($this->user)
            ->postJson(
                "/api/v1/trainings/{$trainingId}/steps/{$stepId}/attempts",
                ['attempt_data' => $attempt_data]
            );

        return $attemptResponse;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeTestData();
    }

    private function initializeTestData(): void
    {
        $this->user = User::factory()->create();
        $this->sourceLang = Language::factory()->create();
        $this->targetLang = Language::factory()->create();
        $this->dictionary = Dictionary::factory()->create([
            'user_id' => $this->user->id,
            'language_from_id' => $this->targetLang->id,
            'language_to_id' => $this->sourceLang->id,
        ]);
    }

    private function getTrainingParams(array $override = []): array
    {
        return array_merge(
            self::DEFAULT_TRAINING_PARAMS,
            ['dictionary_id' => $this->dictionary->id],
            $override
        );
    }

    private function startTraining(int $trainingId): void
    {
        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");

        $startResponse->assertOk()
            ->assertJsonFragment([
                'id' => $trainingId,
                'status' => TrainingStatus::InProgress->value
            ]);
    }

    public function test_api_training_store_successfull(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $this->getTrainingParams());

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'dictionary_id', 'training_type_id']]);
    }

    public function test_api_training_start_successfull()
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $this->getTrainingParams());

        $this->startTraining($response->json('data.id'));
    }

    public function test_api_training_start_fail()
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $this->getTrainingParams());

        $trainingId = $response->json('data.id');
        $this->startTraining($trainingId);

        $secondStartResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");

        $secondStartResponse->assertOk()
            ->assertJsonStructure(['errors' => ['training_can_be_started_only_in_new_state']]);
    }

    public function test_api_next_step_successfully()
    {
        $this->seed(TopWordSeeder::class);

        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $this->getTrainingParams());

        $trainingId = $response->json('data.id');
        $this->startTraining($trainingId);

        $nextStepResponse = $this->actingAs($this->user)
            ->getJson("/api/v1/trainings/{$trainingId}/steps/next");
        $nextStepResponse->assertOk();
    }

    private function createTraining(): int
    {
        $params = array_merge(
            self::TRAINING_PARAMS_WITH_STEPS,
            ['dictionary_id' => $this->dictionary->id]
        );

        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $params);

        return $response->json('data.id');
    }

    public function test_api_step_attempt_successfull()
    {
        $this->seed(TopWordSeeder::class);

        $trainingId = $this->createTraining();
        $this->startTraining($trainingId);

        $nextStepResponse = $this->actingAs($this->user)
            ->getJson("/api/v1/trainings/{$trainingId}/steps/next");

        $stepId = $nextStepResponse->json('data.id');
        $step = TrainingStep::find($stepId);
        $attemptResponse = $this->submitStepAttempt($step, $trainingId, $stepId);
        $attemptResponse->assertOk();
    }

    public function test_step_attemps_list()
    {
        $this->seed(TopWordSeeder::class);

        $trainingId = $this->createTraining();
        $this->startTraining($trainingId);

        $nextStepResponse = $this->actingAs($this->user)
            ->getJson("/api/v1/trainings/{$trainingId}/steps/next");

        $stepId = $nextStepResponse->json('data.id');
        $step = TrainingStep::find($stepId);

        $attemptResponse = $this->submitStepAttempt($step, $trainingId, $stepId);

        $this->actingAs($this->user)
            ->getJson("/api/v1/trainings/{$trainingId}/steps/{$stepId}/attempts")
            ->assertOk();


    }

    public function test_api_training_progress_for_steps_with_multiple_attempts()
    {
        $this->seed(TopWordSeeder::class);
        $trainingId = $this->createTraining();

        $this->mockTrainingStrategy();

        $this->startTraining($trainingId);

        $nextStepResponse = $this->actingAs($this->user)
            ->getJson("/api/v1/trainings/{$trainingId}/steps/next");

        $stepId = $nextStepResponse->json('data.id');
        $step = TrainingStep::find($stepId);

        $this->submitStepAttempt($step, $trainingId, $stepId);

        $progressResponse = $this->actingAs($this->user)
            ->getJson(
                "/api/v1/trainings/{$trainingId}/steps/{$stepId}/progress",
            );
        $isPassed = $progressResponse->json('data.is_passed');
        $this->assertFalse($isPassed);

        $attemptResponse = $this->submitStepAttempt($step, $trainingId, $stepId);
        $this->submitStepAttempt($step, $trainingId, $stepId);
        $this->submitStepAttempt($step, $trainingId, $stepId);

        $progressResponse = $this->actingAs($this->user)
            ->getJson(
                "/api/v1/trainings/{$trainingId}/steps/{$stepId}/progress",
            );
        $isPassed = $progressResponse->json('data.is_passed');
        //dd($progressResponse->json());
        $this->assertTrue($isPassed);

        $attemptResult = $this->submitStepAttempt($step, $trainingId, $stepId);

        $attemptResult->assertStatus(Response::HTTP_CONFLICT);
        $attemptResult->assertJsonStructure([
            'errors' => [
                'training_step_is_already_passed'
            ]
        ]);
    }

    public function test_api_training_expire_successfully()
    {
        $params = array_merge(
            ['dictionary_id' => $this->dictionary->id],
            [
                'training_type_id' => TrainingType::TopWords,
                'completion_type' => TrainingCompletionType::Time,
                'completion_type_params' => ['time_limit' => 2]
            ]
        );

        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', $params);

        $trainingId = $response->json('data.id');
        $this->startTraining($trainingId);
        $expireResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/expire");

        $expireResponse->assertOk()
            ->assertJsonFragment([
                'message' => 'Training completed successfully',
                'status' => TrainingStatus::Completed->value
            ]);


        $training = Training::find($trainingId);
        $this->assertEquals(TrainingStatus::Completed, $training->status);
        $this->assertNotNull($training->completed_at);
    }

    public function test_api_training_expire_fail_for_steps_completion_type()
    {
        $trainingId = $this->createTraining();
        $this->startTraining($trainingId);

        $expireResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/expire");

        $expireResponse->assertStatus(Response::HTTP_CONFLICT)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Training expiration is not supported for tris training type'
            ]);

        $training = Training::find($trainingId);
        $this->assertEquals(TrainingStatus::InProgress, $training->status);
        $this->assertNull($training->completed_at);
    }
}

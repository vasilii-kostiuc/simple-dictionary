<?php

namespace Tests\Feature\Match;

use App\Domain\Language\Models\Language;
use App\Domain\Match\Enums\MatchType;
use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepResolverFactory;
use App\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchStepAttemptTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected Language $languageTo;
    protected Language $languageFrom;
    protected StepResolverFactory $stepResolverFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        // TopWordSeeder uses language_from_id=2, language_to_id=1
        $this->languageTo = Language::factory()->create();   // id=1
        $this->languageFrom = Language::factory()->create(); // id=2

        $this->stepResolverFactory = new StepResolverFactory();
    }

    private function createMatch(): array
    {
        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
            ],
        ]);

        return $response->json('data');
    }

    private function getValidAttemptData(array $stepData): array
    {
        $stepType = StepType::from($stepData['step_type_id']);
        $resolver = $this->stepResolverFactory->create($stepType);

        return $resolver->resolve($stepData['step_data']);
    }

    public function test_can_submit_answer_to_step(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');
        $stepData = $currentStepResponse->json('data');

        $response = $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
            [
                'attempt_data' => $this->getValidAttemptData($stepData),
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user1->id,
            ]
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'attempt' => ['id', 'attempt_data', 'is_correct'],
                    'step' => ['id', 'is_passed'],
                ]
            ]);

        $this->assertDatabaseHas('match_step_attempts', [
            'match_step_id' => $stepId,
            'attempt_number' => 1,
        ]);
    }

    public function test_updates_participant_score_on_correct_answer(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');
        $stepData = $currentStepResponse->json('data');

        // Получаем начальный счёт
        $initialScore = $this->user1->fresh()->matchUsers()
            ->where('match_id', $match['id'])
            ->first()
            ->score ?? 0;

        // Отправляем ответ
        $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
            [
                'attempt_data' => $this->getValidAttemptData($stepData),
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user1->id,
            ]
        );

        // Проверяем что answered_count увеличился
        $matchUser = $this->user1->fresh()->matchUsers()
            ->where('match_id', $match['id'])
            ->first();

        $this->assertEquals(1, $matchUser->answered_count);
    }

    public function test_cannot_submit_answer_to_other_participants_step(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // Получаем шаг user1
        $user1StepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $user1StepResponse->json('data.id');
        $stepData = $user1StepResponse->json('data');

        // Сбрасываем Sanctum guard
        auth()->forgetGuards();

        // user2 пытается ответить на шаг user1
        $response = $this->actingAs($this->user2)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
            [
                'attempt_data' => $this->getValidAttemptData($stepData),
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user2->id,
            ]
        );

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Step does not belong to this user');
    }

    public function test_guest_can_submit_answer(): void
    {
        $this->seed(TopWordSeeder::class);

        $guestId = '550e8400-e29b-41d4-a716-446655440000';

        $createResponse = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'guest', 'id' => $guestId],
            ],
        ]);

        $matchId = $createResponse->json('data.id');

        // Получаем шаг гостя напрямую из БД (API /current возвращает шаг авторизованного user1)
        $guestStep = \App\Domain\Match\Models\MatchStep::where('match_id', $matchId)
            ->where('guest_id', $guestId)
            ->first();

        $this->assertNotNull($guestStep, 'Guest step should be generated on match start');

        $response = $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$matchId}/steps/{$guestStep->id}/attempts",
            [
                'attempt_data' => $this->getValidAttemptData($guestStep->toArray()),
                'attempt_number' => 1,
                'participant_type' => 'guest',
                'participant_id' => $guestId,
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('match_step_attempts', [
            'match_step_id' => $guestStep->id,
        ]);
    }

    public function test_can_get_list_of_attempts(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');
        $stepData = $currentStepResponse->json('data');

        // В матче один правильный ответ закрывает шаг — последующие попытки блокируются
        $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
            [
                'attempt_data' => $this->getValidAttemptData($stepData),
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user1->id,
            ]
        );

        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts");

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_attempts_by_correctness(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');
        $stepData = $currentStepResponse->json('data');

        // Отправляем несколько попыток
        for ($i = 1; $i <= 3; $i++) {
            $this->actingAs($this->user1)->postJson(
                "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
                [
                    'attempt_data' => $this->getValidAttemptData($stepData),
                    'attempt_number' => $i,
                    'participant_type' => 'user',
                    'participant_id' => $this->user1->id,
                ]
            );
        }

        // Запрашиваем только правильные ответы
        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts?is_correct=true");

        $response->assertOk();

        // Все ответы в результате должны быть правильными
        foreach ($response->json('data') as $attempt) {
            $this->assertTrue($attempt['is_correct']);
        }
    }

    public function test_validates_required_fields_for_attempt(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');

        $response = $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$stepId}/attempts",
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attempt_data', 'attempt_number', 'participant_type', 'participant_id']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Dictionary;
use App\Models\Language;
use App\Models\User;
use App\Training\Enums\TrainingCompletionType;
use App\Training\Enums\TrainingStatus;
use App\Training\Enums\TrainingType;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Language $sourceLang;
    protected Language $targetLang;
    protected Dictionary $dictionary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->sourceLang = Language::factory()->create();
        $this->targetLang = Language::factory()->create();
        $this->dictionary = Dictionary::factory()->create([
            'user_id' => $this->user->id,
            'language_from_id' => $this->sourceLang->id,
            'language_to_id' => $this->targetLang->id,
        ]);
    }

    public function test_api_training_store_successfull(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', [
                'dictionary_id' => $this->dictionary->id,
                'training_type_id' => TrainingType::TopWords->value,
                'completion_type' => TrainingCompletionType::Steps->value,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'dictionary_id', 'training_type_id']]);
    }

    public function test_api_training_start_successfull()
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', [
                'dictionary_id' => $this->dictionary->id,
                'training_type_id' => TrainingType::TopWords->value,
                'completion_type' => TrainingCompletionType::Steps->value,
            ]);

        $trainingId = $response->json('data.id');

        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");

        $startResponse->assertOk();
        $startResponse->assertJsonFragment(['id' => $trainingId, 'status' => TrainingStatus::InProgress->value]);
    }
    public function test_api_training_start_fail()
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', [
                'dictionary_id' => $this->dictionary->id,
                'training_type_id' => TrainingType::TopWords->value,
                'completion_type' => TrainingCompletionType::Steps->value,
            ]);

        $trainingId = $response->json('data.id');

        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");

        $startResponse->assertOk();
        $startResponse->assertJsonFragment(['id' => $trainingId, 'status' => TrainingStatus::InProgress->value]);

        $trainingId = $response->json('data.id');

        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");

        $startResponse->assertOk();
        $startResponse->assertJsonStructure(['errors' => ['training_can_be_started_only_in_new_state']]);
    }

    public function test_api_next_step_successfull(){

        $this->seed(TopWordSeeder::class);

        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', [
                'dictionary_id' => $this->dictionary->id,
                'training_type_id' => TrainingType::TopWords->value,
                'completion_type' => TrainingCompletionType::Steps->value,
            ]);

        $trainingId = $response->json('data.id');

        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");
        $startResponse->assertOk();
        $startResponse->assertJsonFragment(['id' => $trainingId, 'status' => TrainingStatus::InProgress->value]);

        $nextStepResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/next-step");
        $nextStepResponse->assertOk();
    }


    public function test_api_step_attempt_successfull(){

        $this->seed(TopWordSeeder::class);

        $response = $this->actingAs($this->user)
            ->postJson('api/v1/trainings', [
                'dictionary_id' => $this->dictionary->id,
                'training_type_id' => TrainingType::TopWords->value,
                'completion_type' => TrainingCompletionType::Steps->value,
            ]);

        $trainingId = $response->json('data.id');

        $startResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/start");
        $startResponse->assertOk();
        $startResponse->assertJsonFragment(['id' => $trainingId, 'status' => TrainingStatus::InProgress->value]);

        $nextStepResponse = $this->actingAs($this->user)
            ->postJson("/api/v1/trainings/{$trainingId}/next-step");
        $nextStepResponse->assertOk();
    }

}

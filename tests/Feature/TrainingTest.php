<?php

namespace Tests\Feature;

use App\Models\Dictionary;
use App\Models\Language;
use App\Models\User;
use App\Training\Enums\TrainingCompletionType;
use App\Training\Enums\TrainingType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_training_store_successfull(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $dictionary = Dictionary::factory()->create([
            'user_id' => $user->id,
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);

        $response = $this->actingAs($user)->postJson('api/v1/trainings' , [
            'dictionary_id' => $dictionary->id,
            'training_type_id' => TrainingType::TopWords->value,
            'completion_type' => TrainingCompletionType::Steps->value,
        ]);

        $response->assertStatus(201)->assertJsonStructure(['data' => ['id', 'dictionary_id', 'training_type_id']]);
    }
}

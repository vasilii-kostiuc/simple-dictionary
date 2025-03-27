<?php

namespace Tests\Feature;

use App\Enums\TrainingType;
use App\Models\Dictionary;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_training_store_successful(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $dictionary = Dictionary::factory()->create([
            'user_id' => $user->id,
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);


        $response = $this->actingAs($user)->post(route('trainings.store'), [
            'dictionary_id' => $dictionary->id,
            'training_type_id' => TrainingType::TopWords->value,
        ]);

        $response->assertStatus(201)->assertJsonStructure(['data' => ['id', 'dictionary_id', 'training_type_id']]);
    }}

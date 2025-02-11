<?php

namespace Tests\Feature;

use App\Models\Dictionary;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class DictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_return_dictionaries_list(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $dictionary = Dictionary::factory()->create([
            'user_id' => $user->id,
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('dictionaries.index'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to']
                ]
            ]);;
    }

    public function test_api_dictionary_show(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $dictionary = Dictionary::factory()->create([
            'user_id' => $user->id,
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);

        $this->assertDatabaseHas('dictionaries', ['id' => $dictionary->id]);

        $response = $this->actingAs($user)->get(route('dictionaries.show', $dictionary->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to']]);
    }

    public function test_api_dictionary_store(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $response = $this->actingAs($user)->postJson(route('dictionaries.store'), [
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to']]);
    }

    public function test_api_dictionary_destroy(): void
    {
        $user = User::factory()->create();

        $langFrom = Language::factory()->create();

        $langTo = Language::factory()->create();

        $dictionary = Dictionary::factory()->create([
            'user_id' => $user->id,
            'language_from_id' => $langFrom->id,
            'language_to_id' => $langTo->id,
        ]);

        $this->assertDatabaseHas('dictionaries', ['id' => $dictionary->id]);

        $response = $this->actingAs($user)->delete(route('dictionaries.destroy', $dictionary->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('dictionaries', ['id' => $dictionary->id]);
    }

}

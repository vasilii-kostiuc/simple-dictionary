<?php

namespace Tests\Feature;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Language\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DictionaryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Language $languageFrom;
    private Language $languageTo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->languageFrom = Language::factory()->create();
        $this->languageTo = Language::factory()->create();
    }

    private function createTestDictionary(): Dictionary
    {
        return Dictionary::factory()->create([
            'user_id' => $this->user->id,
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
        ]);
    }

    public function test_api_return_dictionaries_list_successful(): void
    {
        $this->createTestDictionary();

        $response = $this->actingAs($this->user)->getJson(route('dictionaries.index'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to'],
                ],
            ]);
    }

    public function test_api_dictionary_show_successful(): void
    {
        $dictionary = $this->createTestDictionary();

        $this->assertDatabaseHas('dictionaries', ['id' => $dictionary->id]);

        $response = $this->actingAs($this->user)
            ->get(route('dictionaries.show', $dictionary->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to']
            ]);
    }

    public function test_api_dictionary_store_successful(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('dictionaries.store'), [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'language_from_id', 'language_to_id', 'language_from', 'language_to']
            ]);
    }

    public function test_api_dictionary_destroy_successful(): void
    {
        $dictionary = $this->createTestDictionary();

        $this->assertDatabaseHas('dictionaries', ['id' => $dictionary->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('dictionaries.destroy', $dictionary->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('dictionaries', ['id' => $dictionary->id]);
    }
}

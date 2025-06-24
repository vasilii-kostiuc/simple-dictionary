<?php

namespace Tests\Feature;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Language\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_profile_successful()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('profile.show'));

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => ['name', 'email', 'avatar', 'current_dictionary']]);
    }

    public function test_update_name_email_successful()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'test',
            'email' => 'test@test.com',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'test', 'email' => 'test@test.com']);
    }

    public function test_upload_profile_image_successful()
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('test-avatar.jpg');

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'avatar' => $avatar,
        ]);

        $response->assertStatus(200);
        $user->refresh();

        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_set_current_dictionary_successful()
    {
        $user = User::factory()->create();
        $langFrom = Language::factory()->create();
        $langTo = Language::factory()->create();

        $dictionary1 = Dictionary::factory()->create(['user_id' => $user->id, 'language_from_id' => $langFrom->id, 'language_to_id' => $langTo->id]);

        $dictionary2 = Dictionary::factory()->create(['user_id' => $user->id, 'language_from_id' => $langTo->id, 'language_to_id' => $langFrom->id]);

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'current_dictionary' => $dictionary1->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'current_dictionary' => $dictionary1->id]);
    }

    public function test_set_current_dictionary_from_another_user_fail()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $langFrom = Language::factory()->create();
        $langTo = Language::factory()->create();

        $dictionary1 = Dictionary::factory()->create(['user_id' => $user->id, 'language_from_id' => $langFrom->id, 'language_to_id' => $langTo->id]);

        $dictionary2 = Dictionary::factory()->create(['user_id' => $user2->id, 'language_from_id' => $langTo->id, 'language_to_id' => $langFrom->id]);

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'current_dictionary' => $dictionary2->id,
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('dictionaries', ['id' => $user->id, 'current_dictionary' => $dictionary1->id]);
    }
}

<?php

namespace Tests\Feature\Match;

use App\Core\Language\Models\Language;
use App\Core\Match\Enums\MatchType;
use App\Core\User\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_match_link(): void
    {
        $languageTo = Language::factory()->create();
        $languageFrom = Language::factory()->create();
        $this->seed(TopWordSeeder::class);

        $response = $this->postJson(route('match-links.store'), [
            'language_from_id' => $languageFrom->id,
            'language_to_id' => $languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.participants_limit', 2)
            ->assertJsonPath('data.payload.match_type', MatchType::Time->value)
            ->assertJsonPath('data.status', 'active');

        $this->assertStringContainsString('<svg', $response->json('data.qr_svg'));
        $this->assertDatabaseHas('match_links', [
            'token' => $response->json('data.token'),
            'participants_limit' => 2,
            'created_by_user_id' => null,
        ]);
    }

    public function test_authenticated_user_can_create_match_link(): void
    {
        $user = User::factory()->create();
        $languageTo = Language::factory()->create();
        $languageFrom = Language::factory()->create();
        $this->seed(TopWordSeeder::class);

        $response = $this->actingAs($user)->postJson(route('match-links.store'), [
            'language_from_id' => $languageFrom->id,
            'language_to_id' => $languageTo->id,
            'match_type' => MatchType::Steps->value,
            'match_type_params' => ['steps' => 12],
            'participants_limit' => 4,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.participants_limit', 4);

        $this->assertDatabaseHas('match_links', [
            'token' => $response->json('data.token'),
            'created_by_user_id' => $user->id,
            'participants_limit' => 4,
        ]);
    }

    public function test_can_show_match_link_by_token(): void
    {
        $languageTo = Language::factory()->create();
        $languageFrom = Language::factory()->create();
        $this->seed(TopWordSeeder::class);

        $token = $this->postJson(route('match-links.store'), [
            'language_from_id' => $languageFrom->id,
            'language_to_id' => $languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
        ])->json('data.token');

        $response = $this->getJson(route('match-links.show', ['matchLink' => $token]));

        $response->assertOk()
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.participants_limit', 2);
    }

    public function test_returns_not_found_for_unknown_link(): void
    {
        $response = $this->getJson(route('match-links.show', ['matchLink' => '01JRH4X4TKC5ATZ78QZN4M3C0Y']));

        $response->assertNotFound();
    }

    public function test_validates_participants_limit(): void
    {
        $languageTo = Language::factory()->create();
        $languageFrom = Language::factory()->create();

        $response = $this->postJson(route('match-links.store'), [
            'language_from_id' => $languageFrom->id,
            'language_to_id' => $languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants_limit' => 1,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['participants_limit']);
    }

    public function test_rate_limits_match_link_creation(): void
    {
        $languageTo = Language::factory()->create();
        $languageFrom = Language::factory()->create();
        $payload = [
            'language_from_id' => $languageFrom->id,
            'language_to_id' => $languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
        ];

        $this->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.55',
        ]);

        foreach (range(1, 10) as $attempt) {
            $this->postJson(route('match-links.store'), $payload)->assertCreated();
        }

        $response = $this->postJson(route('match-links.store'), $payload);

        $response->assertStatus(429);
    }
}

<?php

namespace Tests\Feature\Match;

use App\Core\Language\Models\Language;
use App\Core\Match\Enums\{MatchStatus, MatchType};
use App\Core\User\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected Language $languageTo;
    protected Language $languageFrom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        // TopWordSeeder uses language_from_id=2, language_to_id=1
        $this->languageTo = Language::factory()->create();   // id=1
        $this->languageFrom = Language::factory()->create(); // id=2
    }

    public function test_can_create_match_with_authenticated_users(): void
    {
        $this->seed(TopWordSeeder::class);

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

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'language_from_id',
                    'language_to_id',
                    'match_type',
                    'status',
                    'participants',
                ]
            ])
            ->assertJsonPath('data.status', MatchStatus::InProgress->value)
            ->assertJsonPath('data.match_type', MatchType::Time->value);

        $this->assertDatabaseHas('matches', [
            'id' => $response->json('data.id'),
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'status' => MatchStatus::InProgress->value,
        ]);

        $this->assertDatabaseHas('match_users', [
            'match_id' => $response->json('data.id'),
            'user_id' => $this->user1->id,
        ]);

        $this->assertDatabaseHas('match_users', [
            'match_id' => $response->json('data.id'),
            'user_id' => $this->user2->id,
        ]);
    }

    public function test_can_create_match_with_guests(): void
    {
        $this->seed(TopWordSeeder::class);

        $guestId = '550e8400-e29b-41d4-a716-446655440000';

        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 180],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'guest', 'id' => $guestId, 'name' => 'Guest Player'],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('match_users', [
            'match_id' => $response->json('data.id'),
            'guest_id' => $guestId,
            'participant_name' => 'Guest Player',
        ]);
    }

    public function test_can_create_match_with_multiple_participants(): void
    {
        $this->seed(TopWordSeeder::class);

        $user3 = User::factory()->create();
        $guestId = '550e8400-e29b-41d4-a716-446655440001';

        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Steps->value,
            'match_type_params' => ['steps' => 10],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
                ['type' => 'user', 'id' => $user3->id],
                ['type' => 'guest', 'id' => $guestId],
            ],
        ]);

        $response->assertStatus(201);

        $matchId = $response->json('data.id');

        $this->assertDatabaseCount('match_users', 4);
        $this->assertDatabaseHas('match_users', ['match_id' => $matchId, 'user_id' => $this->user1->id]);
        $this->assertDatabaseHas('match_users', ['match_id' => $matchId, 'user_id' => $this->user2->id]);
        $this->assertDatabaseHas('match_users', ['match_id' => $matchId, 'user_id' => $user3->id]);
        $this->assertDatabaseHas('match_users', ['match_id' => $matchId, 'guest_id' => $guestId]);
    }

    public function test_creates_initial_steps_for_all_participants(): void
    {
        $this->seed(TopWordSeeder::class);

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

        $matchId = $response->json('data.id');

        // Проверяем что созданы первые шаги для обоих участников
        $this->assertDatabaseHas('match_steps', [
            'match_id' => $matchId,
            'user_id' => $this->user1->id,
            'step_number' => 1,
        ]);

        $this->assertDatabaseHas('match_steps', [
            'match_id' => $matchId,
            'user_id' => $this->user2->id,
            'step_number' => 1,
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['language_from_id', 'language_to_id', 'match_type', 'match_type_params', 'participants']);
    }

    public function test_validates_minimum_participants(): void
    {
        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['participants']);
    }

    public function test_can_get_active_match_for_user(): void
    {
        $this->seed(TopWordSeeder::class);

        // Создаём матч
        $createResponse = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
            ],
        ]);

        // Получаем активный матч
        $response = $this->actingAs($this->user1)->getJson('/api/v1/matches/active');

        $response->assertOk()
            ->assertJsonPath('data.id', $createResponse->json('data.id'))
            ->assertJsonPath('data.status', MatchStatus::InProgress->value);
    }

    public function test_can_get_active_match_for_guest(): void
    {
        $this->seed(TopWordSeeder::class);

        $guestId = '550e8400-e29b-41d4-a716-446655440000';

        // Создаём матч с гостем
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

        // Получаем активный матч по guest_id
        $response = $this->getJson('/api/v1/matches/active?guest_id='.$guestId);

        $response->assertOk()
            ->assertJsonPath('data.id', $createResponse->json('data.id'));
    }

    public function test_index_returns_empty_when_no_identity_provided(): void
    {
        $this->seed(TopWordSeeder::class);

        // Создаём матч чтобы убедиться что данные есть в БД
        $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
            ],
        ]);

        // Сбрасываем кэш Sanctum guard (кэширует пользователя на инстанции)
        $this->app['auth']->forgetGuards();

        // Запрос без авторизации и без guest_id → должен вернуть пустой массив
        $response = $this->getJson('/api/v1/matches');

        $response->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_index_returns_only_user_matches(): void
    {
        $this->seed(TopWordSeeder::class);

        $user3 = User::factory()->create();

        // Матч user1 + user2
        $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
            ],
        ]);

        // Матч user1 + user3
        $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $user3->id],
            ],
        ]);

        // Сбрасываем кэш Sanctum guard перед сменой пользователя
        $this->app['auth']->forgetGuards();

        // user2 видит только свой 1 матч
        $response = $this->actingAs($this->user2)->getJson('/api/v1/matches');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_returns_only_guest_matches(): void
    {
        $this->seed(TopWordSeeder::class);

        $guestId = '550e8400-e29b-41d4-a716-446655440000';
        $otherGuestId = '550e8400-e29b-41d4-a716-446655440001';

        // Матч user1 + guestId
        $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'guest', 'id' => $guestId],
            ],
        ]);

        // Матч user1 + otherGuestId
        $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'guest', 'id' => $otherGuestId],
            ],
        ]);

        // Сбрасываем кэш Sanctum guard — иначе user1 перекроет guest_id фильтр
        $this->app['auth']->forgetGuards();

        // Запрос без авторизации с guest_id → только его матч
        $response = $this->getJson('/api/v1/matches?guest_id='.$guestId);

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}

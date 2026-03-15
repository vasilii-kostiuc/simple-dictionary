<?php

namespace Tests\Feature\Match;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Language\Models\Language;
use App\Domain\Match\Enums\{MatchStatus, MatchType, MatchCompletionReason};
use App\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected Dictionary $dictionary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $sourceLang = Language::factory()->create();
        $targetLang = Language::factory()->create();

        $this->dictionary = Dictionary::factory()->create([
            'user_id' => $this->user1->id,
            'language_from_id' => $targetLang->id,
            'language_to_id' => $sourceLang->id,
        ]);
    }

    private function createMatch(): array
    {
        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'dictionary_id' => $this->dictionary->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'user', 'id' => $this->user2->id],
            ],
        ]);

        return $response->json('data');
    }

    public function test_can_complete_match(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $response = $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        $response->assertOk()
            ->assertJsonPath('data.status', MatchStatus::Completed->value);

        $this->assertDatabaseHas('matches', [
            'id' => $match['id'],
            'status' => MatchStatus::Completed->value,
        ]);
    }

    public function test_cannot_complete_already_completed_match(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // Завершаем первый раз
        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        // Пытаемся завершить второй раз
        $response = $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Match already completed');
    }

    public function test_can_complete_with_specific_reason(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $response = $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/complete",
            ['reason' => MatchCompletionReason::Forfeited->value]
        );

        $response->assertOk();

        $this->assertDatabaseHas('matches', [
            'id' => $match['id'],
            'completion_reason' => MatchCompletionReason::Forfeited->value,
        ]);
    }

    public function test_completion_determines_winner(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // User1 отвечает на несколько вопросов
        for ($i = 0; $i < 3; $i++) {
            $step = $this->actingAs($this->user1)
                ->getJson("/api/v1/matches/{$match['id']}/steps/current")
                ->json('data');

            $this->actingAs($this->user1)->postJson(
                "/api/v1/matches/{$match['id']}/steps/{$step['id']}/attempts",
                [
                    'answer' => 'test',
                    'attempt_number' => 1,
                    'participant_type' => 'user',
                    'participant_id' => $this->user1->id,
                ]
            );
        }

        // User2 отвечает на 1 вопрос
        $step = $this->actingAs($this->user2)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current")
            ->json('data');

        $this->actingAs($this->user2)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$step['id']}/attempts",
            [
                'answer' => 'test',
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user2->id,
            ]
        );

        // Завершаем матч
        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        // Проверяем что user1 - победитель
        $this->assertDatabaseHas('match_users', [
            'match_id' => $match['id'],
            'user_id' => $this->user1->id,
            'is_winner' => true,
            'place' => 1,
        ]);

        $this->assertDatabaseHas('match_users', [
            'match_id' => $match['id'],
            'user_id' => $this->user2->id,
            'is_winner' => false,
            'place' => 2,
        ]);
    }

    public function test_can_get_match_summary(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // Отвечаем на несколько вопросов
        $step = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current")
            ->json('data');

        $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$step['id']}/attempts",
            [
                'answer' => 'test',
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user1->id,
            ]
        );

        // Завершаем матч
        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        // Получаем summary
        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/summary");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'match_id',
                    'match_time_seconds',
                    'participants',
                    'winner',
                    'completion_reason',
                    'started_at',
                    'completed_at',
                ]
            ])
            ->assertJsonPath('data.match_id', $match['id']);

        // Проверяем что есть информация о победителе
        $this->assertNotNull($response->json('data.winner'));
    }

    public function test_can_list_user_matches(): void
    {
        $this->seed(TopWordSeeder::class);

        // Создаём несколько матчей
        $this->createMatch();
        $this->createMatch();

        $response = $this->actingAs($this->user1)
            ->getJson('/api/v1/matches');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_matches_by_status(): void
    {
        $this->seed(TopWordSeeder::class);

        $match1 = $this->createMatch();
        $match2 = $this->createMatch();

        // Завершаем первый матч
        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match1['id']}/complete");

        // Запрашиваем только активные матчи
        $response = $this->actingAs($this->user1)
            ->getJson('/api/v1/matches?filter[status]=' . MatchStatus::InProgress->value);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match2['id']);
    }

    public function test_summary_includes_all_participants_stats(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // User1 и User2 отвечают на вопросы
        foreach ([$this->user1, $this->user2] as $user) {
            $step = $this->actingAs($user)
                ->getJson("/api/v1/matches/{$match['id']}/steps/current")
                ->json('data');

            $this->actingAs($user)->postJson(
                "/api/v1/matches/{$match['id']}/steps/{$step['id']}/attempts",
                [
                    'answer' => 'test',
                    'attempt_number' => 1,
                    'participant_type' => 'user',
                    'participant_id' => $user->id,
                ]
            );
        }

        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/summary");

        $participants = $response->json('data.participants');

        $this->assertCount(2, $participants);

        foreach ($participants as $participant) {
            $this->assertArrayHasKey('participant_name', $participant);
            $this->assertArrayHasKey('score', $participant);
            $this->assertArrayHasKey('answered_count', $participant);
            $this->assertArrayHasKey('place', $participant);
            $this->assertArrayHasKey('is_winner', $participant);
        }
    }
}

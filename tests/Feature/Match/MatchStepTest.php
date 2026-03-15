<?php

namespace Tests\Feature\Match;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Language\Models\Language;
use App\Domain\Match\Enums\{MatchStatus, MatchType};
use App\Domain\Match\Models\Match;
use App\Models\User;
use Database\Seeders\TopWordSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchStepTest extends TestCase
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

    public function test_can_get_current_step_for_participant(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'match_id',
                    'step_number',
                    'step_type_id',
                    'step_data',
                ]
            ])
            ->assertJsonPath('data.step_number', 1);
    }

    public function test_can_generate_next_step_for_participant(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // Получаем текущий шаг
        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $currentStep = $currentStepResponse->json('data');

        // Отмечаем шаг как пройденный (отправляем правильный ответ)
        $this->actingAs($this->user1)->postJson(
            "/api/v1/matches/{$match['id']}/steps/{$currentStep['id']}/attempts",
            [
                'answer' => 'test_answer',
                'attempt_number' => 1,
                'participant_type' => 'user',
                'participant_id' => $this->user1->id,
            ]
        );

        // Генерируем следующий шаг
        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/next");

        $response->assertOk()
            ->assertJsonPath('data.step_number', 2);

        $this->assertDatabaseHas('match_steps', [
            'match_id' => $match['id'],
            'user_id' => $this->user1->id,
            'step_number' => 2,
        ]);
    }

    public function test_cannot_generate_next_step_if_completed_match(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        // Завершаем матч
        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/complete");

        // Пытаемся получить следующий шаг
        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/next");

        $response->assertStatus(409)
            ->assertJsonPath('errors.match_finished', 'Match is finished');
    }

    public function test_can_skip_step(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');

        $response = $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match['id']}/steps/{$stepId}/skip");

        $response->assertOk()
            ->assertJsonPath('data.skipped', true);

        $this->assertDatabaseHas('match_steps', [
            'id' => $stepId,
            'skipped' => true,
        ]);
    }

    public function test_guest_can_get_current_step(): void
    {
        $this->seed(TopWordSeeder::class);

        $guestId = '550e8400-e29b-41d4-a716-446655440000';

        $response = $this->actingAs($this->user1)->postJson('/api/v1/matches', [
            'dictionary_id' => $this->dictionary->id,
            'match_type' => MatchType::Time->value,
            'match_type_params' => ['duration' => 300],
            'participants' => [
                ['type' => 'user', 'id' => $this->user1->id],
                ['type' => 'guest', 'id' => $guestId],
            ],
        ]);

        $matchId = $response->json('data.id');

        $currentStepResponse = $this->getJson("/api/v1/matches/{$matchId}/steps/current?guest_id={$guestId}");

        $currentStepResponse->assertOk()
            ->assertJsonStructure(['data' => ['id', 'step_number', 'step_data']]);
    }

    public function test_different_participants_have_different_steps(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $user1Step = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current")
            ->json('data');

        $user2Step = $this->actingAs($this->user2)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current")
            ->json('data');

        // Шаги должны быть разными (разные ID, но оба step_number = 1)
        $this->assertNotEquals($user1Step['id'], $user2Step['id']);
        $this->assertEquals(1, $user1Step['step_number']);
        $this->assertEquals(1, $user2Step['step_number']);
    }

    public function test_can_show_specific_step(): void
    {
        $this->seed(TopWordSeeder::class);

        $match = $this->createMatch();

        $currentStepResponse = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/current");

        $stepId = $currentStepResponse->json('data.id');

        $response = $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match['id']}/steps/{$stepId}");

        $response->assertOk()
            ->assertJsonPath('data.id', $stepId);
    }
}

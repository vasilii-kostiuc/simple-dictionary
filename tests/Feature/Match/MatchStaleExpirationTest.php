<?php

namespace Tests\Feature\Match;

use App\Core\Language\Models\Language;
use App\Core\Match\Enums\MatchCompletionReason;
use App\Core\Match\Enums\MatchStatus;
use App\Core\Match\Enums\MatchType;
use App\Core\Match\Enums\MatchUserStatus;
use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchStep;
use App\Core\Match\Models\MatchStepAttempt;
use App\Core\Match\Models\MatchUser;
use App\Core\User\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MatchStaleExpirationTest extends TestCase
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
        $this->languageTo = Language::factory()->create();
        $this->languageFrom = Language::factory()->create();

        Config::set('matches.stale.first_action_timeout_seconds', 60);
        Config::set('matches.stale.steps_inactivity_timeout_seconds', 60);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_time_match_without_actions_is_completed_as_not_held(): void
    {
        Carbon::setTestNow(now());

        $match = $this->createInProgressMatch(
            MatchType::Time,
            ['duration' => 300],
            now()->subSeconds(61)
        );

        $this->artisan('matches:expire-stale')->assertSuccessful();

        $match->refresh();

        $this->assertSame(MatchStatus::Completed, $match->status);
        $this->assertSame(MatchCompletionReason::NotHeld, $match->completion_reason);
        $this->assertNull($match->matchUsers()->where('is_winner', true)->first());
        $this->assertTrue($match->matchUsers()->whereNotNull('place')->doesntExist());

        $this->actingAs($this->user1)
            ->getJson("/api/v1/matches/{$match->id}/summary")
            ->assertOk()
            ->assertJsonPath('data.completion_reason', MatchCompletionReason::NotHeld->value)
            ->assertJsonPath('data.winner', null);
    }

    public function test_steps_match_with_only_skips_is_completed_as_not_held(): void
    {
        Carbon::setTestNow(now());

        $match = $this->createInProgressMatch(
            MatchType::Steps,
            ['steps' => 10],
            now()->subMinutes(5)
        );

        $this->createSkippedStep($match, $this->user1->id, 1, now()->subSeconds(70));

        $this->artisan('matches:expire-stale')->assertSuccessful();

        $match->refresh();

        $this->assertSame(MatchCompletionReason::NotHeld, $match->completion_reason);
        $this->assertNull($match->matchUsers()->where('is_winner', true)->first());
    }

    public function test_steps_match_with_attempts_is_completed_as_no_activity(): void
    {
        Carbon::setTestNow(now());

        $match = $this->createInProgressMatch(
            MatchType::Steps,
            ['steps' => 10],
            now()->subMinutes(5)
        );

        $step = $this->createStep($match, $this->user1->id, 1);
        $this->createAttempt($step, now()->subSeconds(70), true);

        $matchUser = $match->matchUsers()->where('user_id', $this->user1->id)->firstOrFail();
        $matchUser->answered_count = 1;
        $matchUser->correct_answers_count = 1;
        $matchUser->score = 1;
        $matchUser->save();

        $this->artisan('matches:expire-stale')->assertSuccessful();

        $match->refresh();

        $this->assertSame(MatchCompletionReason::NoActivity, $match->completion_reason);
        $this->assertNotNull($match->matchUsers()->where('is_winner', true)->first());
    }

    public function test_recent_steps_match_is_not_completed_by_command(): void
    {
        Carbon::setTestNow(now());

        $match = $this->createInProgressMatch(
            MatchType::Steps,
            ['steps' => 10],
            now()->subSeconds(30)
        );

        $step = $this->createStep($match, $this->user1->id, 1);
        $this->createAttempt($step, now()->subSeconds(20), false);

        $this->artisan('matches:expire-stale')->assertSuccessful();

        $match->refresh();

        $this->assertSame(MatchStatus::InProgress, $match->status);
        $this->assertNull($match->completion_reason);
    }

    public function test_manual_complete_without_attempts_is_normalized_to_not_held(): void
    {
        Carbon::setTestNow(now());

        $match = $this->createInProgressMatch(
            MatchType::Steps,
            ['steps' => 10],
            now()->subMinutes(5)
        );

        $this->actingAs($this->user1)
            ->postJson("/api/v1/matches/{$match->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.completion_reason', MatchCompletionReason::NotHeld->value);

        $match->refresh();

        $this->assertSame(MatchCompletionReason::NotHeld, $match->completion_reason);
        $this->assertNull($match->matchUsers()->where('is_winner', true)->first());
    }

    private function createInProgressMatch(MatchType $type, array $params, Carbon $startedAt): MatchModel
    {
        $match = MatchModel::create([
            'language_from_id' => $this->languageFrom->id,
            'language_to_id' => $this->languageTo->id,
            'dictionary_id' => null,
            'match_type' => $type,
            'match_type_params' => $params,
            'status' => MatchStatus::InProgress,
            'started_at' => $startedAt,
        ]);

        foreach ([$this->user1, $this->user2] as $user) {
            MatchUser::create([
                'match_id' => $match->id,
                'user_id' => $user->id,
                'participant_name' => $user->name,
                'participant_avatar' => $user->avatar,
                'status' => MatchUserStatus::Active,
                'joined_at' => $startedAt,
            ]);
        }

        return $match->fresh(['matchUsers', 'steps']);
    }

    private function createStep(MatchModel $match, int $userId, int $stepNumber): MatchStep
    {
        return MatchStep::create([
            'match_id' => $match->id,
            'user_id' => $userId,
            'guest_id' => null,
            'step_type_id' => 1,
            'step_number' => $stepNumber,
            'step_data' => ['word' => 'child', 'answers' => []],
            'required_answers_count' => 1,
            'skipped' => false,
            'skipped_at' => null,
        ]);
    }

    private function createSkippedStep(MatchModel $match, int $userId, int $stepNumber, Carbon $skippedAt): MatchStep
    {
        $step = MatchStep::create([
            'match_id' => $match->id,
            'user_id' => $userId,
            'guest_id' => null,
            'step_type_id' => 1,
            'step_number' => $stepNumber,
            'step_data' => ['word' => 'child', 'answers' => []],
            'required_answers_count' => 1,
            'skipped' => true,
            'skipped_at' => $skippedAt,
        ]);

        $step->created_at = $skippedAt;
        $step->updated_at = $skippedAt;
        $step->saveQuietly();

        return $step;
    }

    private function createAttempt(MatchStep $step, Carbon $createdAt, bool $isCorrect): MatchStepAttempt
    {
        $attempt = MatchStepAttempt::create([
            'match_step_id' => $step->id,
            'attempt_number' => 1,
            'sub_index' => 1,
            'attempt_data' => ['word_id' => 1],
            'is_correct' => $isCorrect,
        ]);

        $attempt->created_at = $createdAt;
        $attempt->updated_at = $createdAt;
        $attempt->saveQuietly();

        return $attempt;
    }
}

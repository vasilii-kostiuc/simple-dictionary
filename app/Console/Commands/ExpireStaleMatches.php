<?php

namespace App\Console\Commands;

use App\Domain\Match\Actions\CompleteMatchAction;
use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Services\MatchCompletionReasonSelector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireStaleMatches extends Command
{
    protected $signature = 'matches:expire-stale';

    protected $description = 'Complete stale in-progress matches based on action timeouts';

    public function __construct(
        private readonly MatchCompletionReasonSelector $matchCompletionReasonSelector,
        private readonly CompleteMatchAction $completeMatchAction
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $expiredMatches = 0;
        $firstActionTimeout = (int) config('matches.stale.first_action_timeout_seconds');
        $stepsInactivityTimeout = (int) config('matches.stale.steps_inactivity_timeout_seconds');

        $startMessage = sprintf(
            'Starting stale match expiration scan. first_action_timeout_seconds=%d, steps_inactivity_timeout_seconds=%d',
            $firstActionTimeout,
            $stepsInactivityTimeout
        );

        $this->info($startMessage);
        Log::info($startMessage, [
            'command' => $this->getName(),
            'first_action_timeout_seconds' => $firstActionTimeout,
            'steps_inactivity_timeout_seconds' => $stepsInactivityTimeout,
        ]);

        MatchModel::query()
            ->where('status', MatchStatus::InProgress)
            ->whereNotNull('started_at')
            ->with(['matchUsers', 'steps.attempts'])
            ->chunkById(100, function ($matches) use (&$expiredMatches) {
                foreach ($matches as $match) {
                    $reason = $this->matchCompletionReasonSelector->resolveStaleReason($match);
                    if ($reason === null) {
                        continue;
                    }

                    $details = ['expired_by' => 'scheduler'];
                    $lastActionAt = $this->matchCompletionReasonSelector->lastActionAt($match);

                    if ($lastActionAt !== null) {
                        $details['last_action_at'] = $lastActionAt->toISOString();
                    }

                    $this->completeMatchAction->handle($match, $reason, $details);
                    $expiredMatches++;

                    $expiredMessage = sprintf(
                        'Auto-completed stale match #%d with reason "%s".',
                        $match->id,
                        $reason->value
                    );

                    $this->line($expiredMessage);
                    Log::info($expiredMessage, [
                        'command' => $this->getName(),
                        'match_id' => $match->id,
                        'reason' => $reason->value,
                        'last_action_at' => $details['last_action_at'] ?? null,
                    ]);
                }
            });

        $finishMessage = "Expired {$expiredMatches} stale match(es).";

        $this->info($finishMessage);
        Log::info($finishMessage, [
            'command' => $this->getName(),
            'expired_matches_count' => $expiredMatches,
        ]);

        return self::SUCCESS;
    }
}

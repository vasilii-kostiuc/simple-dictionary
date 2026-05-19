<?php

namespace App\Domain\Match\Services;

use App\Domain\Match\Enums\MatchCompletionReason;
use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Enums\MatchType;
use App\Domain\Match\Models\MatchModel;
use Carbon\CarbonInterface;

class MatchCompletionReasonSelector
{
    public function normalizeCompletionReason(MatchModel $match, ?MatchCompletionReason $reason = null): MatchCompletionReason
    {
        if ($reason !== null) {
            return $reason;
        }

        if ($this->hasAttempts($match)) {
            return MatchCompletionReason::defaultForMatchType($match->match_type);
        }

        return MatchCompletionReason::NotHeld;
    }

    public function resolveStaleReason(MatchModel $match): ?MatchCompletionReason
    {
        if ($match->status !== MatchStatus::InProgress || $match->started_at === null) {
            return null;
        }

        if (! $this->hasAttempts($match)) {
            if (! $this->hasAnyAction($match) && $this->firstActionTimeoutExceeded($match)) {
                return MatchCompletionReason::NotHeld;
            }

            if ($match->match_type === MatchType::Time && $this->timeMatchExpired($match)) {
                return MatchCompletionReason::NotHeld;
            }

            if ($match->match_type === MatchType::Steps && $this->stepsInactivityExceeded($match)) {
                return MatchCompletionReason::NotHeld;
            }

            return null;
        }

        if ($match->match_type === MatchType::Steps && $this->stepsInactivityExceeded($match)) {
            return MatchCompletionReason::NoActivity;
        }

        return null;
    }

    public function hasAttempts(MatchModel $match): bool
    {
        $match->loadMissing('steps.attempts');

        return $match->steps->contains(fn ($step) => $step->attempts->isNotEmpty());
    }

    public function hasAnyAction(MatchModel $match): bool
    {
        $match->loadMissing('steps.attempts');

        return $match->steps->contains(fn ($step) => $step->skipped_at !== null || $step->attempts->isNotEmpty());
    }

    public function lastActionAt(MatchModel $match): ?CarbonInterface
    {
        $match->loadMissing('steps.attempts');

        $timestamps = [];

        foreach ($match->steps as $step) {
            if ($step->skipped_at !== null) {
                $timestamps[] = $step->skipped_at;
            }

            foreach ($step->attempts as $attempt) {
                $timestamps[] = $attempt->created_at;
            }
        }

        if ($timestamps === []) {
            return null;
        }

        usort(
            $timestamps,
            fn (CarbonInterface $left, CarbonInterface $right) => $left->lessThan($right) ? 1 : -1
        );

        return $timestamps[0];
    }

    private function firstActionTimeoutExceeded(MatchModel $match): bool
    {
        $timeoutSeconds = (int) config('matches.stale.first_action_timeout_seconds', 300);

        return $match->started_at->copy()->addSeconds($timeoutSeconds)->isPast();
    }

    private function stepsInactivityExceeded(MatchModel $match): bool
    {
        $lastActionAt = $this->lastActionAt($match);
        if ($lastActionAt === null) {
            return false;
        }

        $timeoutSeconds = (int) config('matches.stale.steps_inactivity_timeout_seconds', 300);

        return $lastActionAt->copy()->addSeconds($timeoutSeconds)->isPast();
    }

    private function timeMatchExpired(MatchModel $match): bool
    {
        $durationSeconds = (int) ($match->match_type_params['duration'] ?? 0);

        if ($durationSeconds <= 0) {
            return false;
        }

        return $match->started_at->copy()->addSeconds($durationSeconds)->isPast();
    }
}

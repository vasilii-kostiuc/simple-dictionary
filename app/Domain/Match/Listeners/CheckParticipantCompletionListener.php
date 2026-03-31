<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Enums\MatchType;
use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Shared\CompletionConditions\StepsCompletionCondition;

class CheckParticipantCompletionListener
{
    public function handle(MatchUserAnsweredEvent|MatchStepSkippedEvent $event): void
    {
        $match = $event->match->refresh();

        if (! in_array($match->match_type, [MatchType::Steps, MatchType::Race], true)) {
            return;
        }

        [$userId, $guestId] = $this->resolveParticipant($event);

        $matchUser = $match->matchUsers
            ->first(fn (MatchUser $mu) => $userId
                ? $mu->user_id === $userId
                : $mu->guest_id === $guestId
            );

        if ($matchUser === null || $matchUser->status->isTerminal()) {
            return;
        }

        $participantSteps = $match->steps->filter(fn ($step) => $userId
            ? $step->user_id === $userId
            : $step->guest_id === $guestId
        );

        $requiredStepsCount = $match->match_type_params['steps'];

        $condition = new StepsCompletionCondition($requiredStepsCount, $participantSteps, true);

        if ($condition->isCompleted()) {
            $matchUser->finish();
        }
    }

    private function resolveParticipant(MatchUserAnsweredEvent|MatchStepSkippedEvent $event): array
    {
        if ($event instanceof MatchUserAnsweredEvent) {
            return [$event->matchUser->user_id, $event->matchUser->guest_id];
        }

        return [$event->step->user_id, $event->step->guest_id];
    }
}

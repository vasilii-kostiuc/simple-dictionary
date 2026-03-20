<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Events\MatchNextStepGeneratedEvent;
use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Service\MatchStepService;

class MatchUserAnsweredListener
{
    public function __construct(
        private MatchStepService $matchStepService
    ) {
    }

    public function handle(MatchUserAnsweredEvent $event): void
    {
        $match = $event->match->refresh();

        if (in_array($match->status, [MatchStatus::Completed, MatchStatus::Cancelled], true)) {
            return;
        }

        $nextStep = $this->matchStepService->generateNextStepForParticipant(
            $match,
            $event->matchUser->user_id,
            $event->matchUser->guest_id
        );

        event(new MatchNextStepGeneratedEvent($match, $nextStep));
    }
}

<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Events\MatchNextStepGeneratedEvent;
use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Service\MatchStepService;

class MatchStepSkippedListener
{
    public function __construct(
        private MatchStepService $matchStepService
    ) {
    }

    public function handle(MatchStepSkippedEvent $event): void
    {
        $match = $event->match->refresh();

        if (in_array($match->status, [MatchStatus::Completed, MatchStatus::Cancelled], true)) {
            return;
        }

        $nextStep = $this->matchStepService->generateNextStepForParticipant(
            $match,
            $event->step->user_id,
            $event->step->guest_id
        );

        event(new MatchNextStepGeneratedEvent($match, $nextStep));
    }
}

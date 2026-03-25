<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Events\MatchNextStepGeneratedEvent;
use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Service\MatchStepService;

class GenerateMatchNextStepOnParticipantActionListener
{
    public function __construct(private MatchStepService $matchStepService) {}

    public function handle(MatchUserAnsweredEvent|MatchStepSkippedEvent $event): void
    {
        $match = $event->match->refresh();

        if (in_array($match->status, [MatchStatus::Completed, MatchStatus::Cancelled], true)) {
            return;
        }

        [$userId, $guestId] = match (true) {
            $event instanceof MatchUserAnsweredEvent => [$event->matchUser->user_id, $event->matchUser->guest_id],
            $event instanceof MatchStepSkippedEvent  => [$event->step->user_id, $event->step->guest_id],
        };

        $nextStep = $this->matchStepService->generateNextStepForParticipant($match, $userId, $guestId);

        event(new MatchNextStepGeneratedEvent($match, $nextStep));
    }
}

<?php

namespace App\Core\Match\Actions;

use App\Core\Match\DTO\ParticipantIdentifier;
use App\Core\Match\Enums\MatchStatus;
use App\Core\Match\Events\MatchNextStepGeneratedEvent;
use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchStep;
use App\Core\Match\Models\MatchUser;
use App\Core\Match\Services\MatchStepService;

class GenerateNextMatchStepAction
{
    public function __construct(
        private readonly MatchStepService $matchStepService
    ) {}

    public function handle(MatchModel $match, ParticipantIdentifier $participant, bool $dispatchEvent = false): ?MatchStep
    {
        $match = $match->refresh();

        if (in_array($match->status, [MatchStatus::Completed, MatchStatus::Cancelled], true)) {
            return null;
        }

        $match->loadMissing('matchUsers');

        $matchUser = $match->matchUsers->first(fn (MatchUser $mu) => $participant->userId
            ? $mu->user_id === $participant->userId
            : $mu->guest_id === $participant->guestId);

        if ($matchUser !== null && $matchUser->status->isTerminal()) {
            return null;
        }

        $nextStep = $this->matchStepService->generateNextStepForParticipant($match, $participant);

        if ($dispatchEvent) {
            event(new MatchNextStepGeneratedEvent($match, $nextStep));
        }

        return $nextStep;
    }
}

<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Events\MatchNextStepGeneratedEvent;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Match\Services\MatchStepService;

class GenerateNextMatchStepAction
{
    public function __construct(
        private readonly MatchStepService $matchStepService
    ) {
    }

    public function handle(MatchModel $match, ?int $userId, ?string $guestId, bool $dispatchEvent = false): ?MatchStep
    {
        $match = $match->refresh();

        if (in_array($match->status, [MatchStatus::Completed, MatchStatus::Cancelled], true)) {
            return null;
        }

        $match->loadMissing('matchUsers');

        $matchUser = $match->matchUsers->first(fn (MatchUser $participant) => $userId
            ? $participant->user_id === $userId
            : $participant->guest_id === $guestId);

        if ($matchUser !== null && $matchUser->status->isTerminal()) {
            return null;
        }

        $nextStep = $this->matchStepService->generateNextStepForParticipant($match, $userId, $guestId);

        if ($dispatchEvent) {
            event(new MatchNextStepGeneratedEvent($match, $nextStep));
        }

        return $nextStep;
    }
}

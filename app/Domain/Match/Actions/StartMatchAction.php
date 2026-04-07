<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\DTO\ParticipantIdentifier;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Services\MatchService;

class StartMatchAction
{
    public function __construct(
        private readonly MatchService $matchService,
        private readonly GenerateNextMatchStepAction $generateNextMatchStepAction
    ) {
    }

    public function handle(MatchModel $match): MatchModel
    {
        $match = $this->matchService->start($match);
        $match->loadMissing('matchUsers');

        foreach ($match->matchUsers as $matchUser) {
            $participant = ParticipantIdentifier::fromMatchUser($matchUser);

            $hasExistingStep = $match->steps()
                ->where(function ($query) use ($participant) {
                    if ($participant->userId !== null) {
                        $query->where('user_id', $participant->userId);
                        return;
                    }

                    $query->where('guest_id', $participant->guestId);
                })
                ->exists();

            if ($hasExistingStep) {
                continue;
            }

            $this->generateNextMatchStepAction->handle($match, $participant, false);
        }

        return $match->refresh();
    }
}

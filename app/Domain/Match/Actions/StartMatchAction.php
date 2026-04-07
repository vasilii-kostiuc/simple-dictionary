<?php

namespace App\Domain\Match\Actions;

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
            $hasExistingStep = $match->steps()
                ->where(function ($query) use ($matchUser) {
                    if ($matchUser->user_id !== null) {
                        $query->where('user_id', $matchUser->user_id);

                        return;
                    }

                    $query->where('guest_id', $matchUser->guest_id);
                })
                ->exists();

            if ($hasExistingStep) {
                continue;
            }

            $this->generateNextMatchStepAction->handle(
                $match,
                $matchUser->user_id,
                $matchUser->guest_id,
                false
            );
        }

        return $match->refresh();
    }
}

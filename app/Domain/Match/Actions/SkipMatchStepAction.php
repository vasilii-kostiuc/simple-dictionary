<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\DTO\ParticipantIdentifier;
use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Factories\CompletionConditionFactory;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Match\Services\MatchStepAttemptService;
use App\Domain\Match\Services\MatchStepService;

class SkipMatchStepAction
{
    public function __construct(
        private readonly MatchStepService $matchStepService,
        private readonly MatchStepAttemptService $matchStepAttemptService,
        private readonly CompletionConditionFactory $completionConditionFactory,
        private readonly CompleteMatchAction $completeMatchAction,
        private readonly GenerateNextMatchStepAction $generateNextMatchStepAction
    ) {
    }

    public function handle(MatchStep $step): MatchStep
    {
        $step = $this->matchStepService->skip($step);
        $match = $step->match->refresh()->loadMissing('matchUsers', 'steps');
        $participant = ParticipantIdentifier::fromMatchStep($step);

        event(new MatchStepSkippedEvent($match, $step));

        $matchUser = $this->matchStepAttemptService->finishParticipantIfNeeded($match, $participant);

        if ($this->completionConditionFactory->create($match)->isCompleted()) {
            $this->completeMatchAction->handle($match);

            return $step->refresh();
        }

        if ($matchUser === null || ! $matchUser->status->isTerminal()) {
            $this->generateNextMatchStepAction->handle($match, $participant, true);
        }

        return $step->refresh();
    }
}

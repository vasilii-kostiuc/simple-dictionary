<?php

namespace App\Core\Match\Actions;

use App\Core\Match\DTO\ParticipantIdentifier;
use App\Core\Match\Events\MatchUserAnsweredEvent;
use App\Core\Match\Factories\CompletionConditionFactory;
use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchStep;
use App\Core\Match\Models\MatchStepAttempt;
use App\Core\Match\Models\MatchUser;
use App\Core\Match\Services\MatchStepAttemptService;

class SubmitMatchAttemptAction
{
    public function __construct(
        private readonly MatchStepAttemptService $matchStepAttemptService,
        private readonly CompletionConditionFactory $completionConditionFactory,
        private readonly CompleteMatchAction $completeMatchAction,
        private readonly GenerateNextMatchStepAction $generateNextMatchStepAction
    ) {}

    public function handle(MatchStep $step, array $attemptData, int $attemptNumber): MatchStepAttempt
    {
        $attempt = $this->matchStepAttemptService->submitAnswer($step, $attemptData, $attemptNumber);
        $match = $step->match->refresh()->loadMissing('matchUsers', 'steps');
        $matchUser = $this->matchStepAttemptService->resolveParticipant($step);
        $participant = ParticipantIdentifier::fromMatchStep($step);

        $this->dispatchAnsweredEvent($match, $step, $attempt, $matchUser);

        $matchUser = $this->matchStepAttemptService->finishParticipantIfNeeded($match, $participant);

        $this->completeOrAdvance($match, $matchUser, $participant);

        return $attempt;
    }

    private function dispatchAnsweredEvent(MatchModel $match, MatchStep $step, MatchStepAttempt $attempt, ?MatchUser $matchUser): void
    {
        if ($matchUser === null) {
            return;
        }

        event(new MatchUserAnsweredEvent(
            $match,
            $step->getParticipantIdentifier(),
            $attempt->is_correct,
            $matchUser
        ));
    }

    private function completeOrAdvance(MatchModel $match, ?MatchUser $matchUser, ParticipantIdentifier $participant): void
    {
        if ($this->completionConditionFactory->create($match)->isCompleted()) {
            $this->completeMatchAction->handle($match);

            return;
        }

        if ($matchUser === null || ! $matchUser->status->isTerminal()) {
            $this->generateNextMatchStepAction->handle($match, $participant, true);
        }
    }
}

<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\Enums\MatchType;
use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Factories\CompletionConditionFactory;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Models\MatchStepAttempt;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Match\Services\MatchStepAttemptService;
use App\Domain\Shared\CompletionConditions\StepsCompletionCondition;

class SubmitMatchAttemptAction
{
    public function __construct(
        private readonly MatchStepAttemptService $matchStepAttemptService,
        private readonly CompletionConditionFactory $completionConditionFactory,
        private readonly CompleteMatchAction $completeMatchAction,
        private readonly GenerateNextMatchStepAction $generateNextMatchStepAction
    ) {
    }

    public function handle(MatchStep $step, array $attemptData, int $attemptNumber): MatchStepAttempt
    {
        $attempt = $this->matchStepAttemptService->submitAnswer($step, $attemptData, $attemptNumber);
        $match = $step->match->refresh();
        $match->loadMissing('matchUsers', 'steps');
        $matchUser = $this->matchStepAttemptService->resolveParticipant($step);

        if ($matchUser !== null) {
            event(new MatchUserAnsweredEvent(
                $match,
                $step->getParticipantIdentifier(),
                $attempt->is_correct,
                $matchUser
            ));
        }

        $matchUser = $this->finishParticipantIfNeeded($match, $step->user_id, $step->guest_id);

        if ($this->completionConditionFactory->create($match)->isCompleted()) {
            $this->completeMatchAction->handle($match);

            return $attempt;
        }

        if ($matchUser === null || ! $matchUser->status->isTerminal()) {
            $this->generateNextMatchStepAction->handle($match, $step->user_id, $step->guest_id, true);
        }

        return $attempt;
    }

    private function finishParticipantIfNeeded(MatchModel $match, ?int $userId, ?string $guestId): ?MatchUser
    {
        if (! in_array($match->match_type, [MatchType::Steps, MatchType::Race], true)) {
            return $this->findParticipant($match, $userId, $guestId);
        }

        $matchUser = $this->findParticipant($match, $userId, $guestId);

        if ($matchUser === null || $matchUser->status->isTerminal()) {
            return $matchUser;
        }

        $participantSteps = $match->steps->filter(fn ($participantStep) => $userId
            ? $participantStep->user_id === $userId
            : $participantStep->guest_id === $guestId);

        $requiredStepsCount = (int) ($match->match_type_params['steps'] ?? 0);
        $condition = new StepsCompletionCondition($requiredStepsCount, $participantSteps, true);

        if ($condition->isCompleted()) {
            $matchUser->finish();
        }

        return $matchUser->refresh();
    }

    private function findParticipant(MatchModel $match, ?int $userId, ?string $guestId): ?MatchUser
    {
        return $match->matchUsers->first(fn (MatchUser $participant) => $userId
            ? $participant->user_id === $userId
            : $participant->guest_id === $guestId);
    }
}

<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\Enums\MatchType;
use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Factories\CompletionConditionFactory;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Match\Services\MatchStepService;
use App\Domain\Shared\CompletionConditions\StepsCompletionCondition;

class SkipMatchStepAction
{
    public function __construct(
        private readonly MatchStepService $matchStepService,
        private readonly CompletionConditionFactory $completionConditionFactory,
        private readonly CompleteMatchAction $completeMatchAction,
        private readonly GenerateNextMatchStepAction $generateNextMatchStepAction
    ) {
    }

    public function handle(MatchStep $step): MatchStep
    {
        $step = $this->matchStepService->skip($step);
        $match = $step->match->refresh();
        $match->loadMissing('matchUsers', 'steps');

        event(new MatchStepSkippedEvent($match, $step));

        $matchUser = $this->finishParticipantIfNeeded($match, $step->user_id, $step->guest_id);

        if ($this->completionConditionFactory->create($match)->isCompleted()) {
            $this->completeMatchAction->handle($match);

            return $step->refresh();
        }

        if ($matchUser === null || ! $matchUser->status->isTerminal()) {
            $this->generateNextMatchStepAction->handle($match, $step->user_id, $step->guest_id, true);
        }

        return $step->refresh();
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

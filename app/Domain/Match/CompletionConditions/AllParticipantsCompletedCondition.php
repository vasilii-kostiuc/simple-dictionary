<?php

namespace App\Domain\Match\CompletionConditions;

use App\Domain\Shared\CompletionConditions\CompletionConditionInterface;
use App\Domain\Shared\CompletionConditions\StepsCompletionCondition;
use Illuminate\Support\Collection;

class AllParticipantsCompletedCondition implements CompletionConditionInterface
{
    public function __construct(
        private readonly int $requiredStepsCount,
        private readonly Collection $matchUsers,
        private readonly Collection $steps,
    ) {
    }

    public function isCompleted(): bool
    {
        foreach ($this->matchUsers as $matchUser) {
            $participantSteps = $this->getStepsForParticipant($matchUser);

            $condition = new StepsCompletionCondition($this->requiredStepsCount, $participantSteps, true);

            if (! $condition->isCompleted()) {
                return false;
            }
        }

        return true;
    }

    private function getStepsForParticipant(mixed $matchUser): Collection
    {
        return $this->steps->filter(function ($step) use ($matchUser) {
            if ($matchUser->user_id) {
                return $step->user_id === $matchUser->user_id;
            }

            return $step->guest_id === $matchUser->guest_id;
        });
    }
}

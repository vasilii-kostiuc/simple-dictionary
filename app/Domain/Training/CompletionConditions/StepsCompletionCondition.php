<?php

namespace App\Domain\Training\CompletionConditions;

use Illuminate\Support\Collection;

class StepsCompletionCondition implements CompletionConditionInterface
{
    protected readonly int $requiredStepsCount;
    protected readonly Collection $trainingSteps;

    public function __construct(int $requiredStepsCount, Collection $trainingSteps)
    {
        $this->requiredStepsCount = $requiredStepsCount;
        $this->trainingSteps = $trainingSteps;
    }

    public function isCompleted(): bool
    {
        if ($this->trainingSteps->count() < $this->requiredStepsCount) {
            return false;
        }

        $notPassedOrSkippedStepsCount = $this->trainingSteps->filter(fn($trainingStep) => !($trainingStep->isPassed() || $trainingStep->is_skipped))->count();

        return $notPassedOrSkippedStepsCount === 0;
    }
}

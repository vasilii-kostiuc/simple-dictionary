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

        $skippedOrPassedCount = $this->trainingSteps->filter(fn($trainingStep) => ($trainingStep->isPassed() || $trainingStep->skipped))->count();

        return $skippedOrPassedCount >= $this->requiredStepsCount;
    }
}

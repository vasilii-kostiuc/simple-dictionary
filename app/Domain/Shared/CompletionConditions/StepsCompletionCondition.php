<?php

namespace App\Domain\Shared\CompletionConditions;

use Illuminate\Support\Collection;

class StepsCompletionCondition implements CompletionConditionInterface
{
    protected readonly int $requiredStepsCount;
    protected readonly Collection $steps;
    protected readonly bool $withAttempted;

    public function __construct(int $requiredStepsCount, Collection $steps, bool $withAttempted = false)
    {
        $this->requiredStepsCount = $requiredStepsCount;
        $this->steps = $steps;
        $this->withAttempted = $withAttempted;
    }

    public function isCompleted(): bool
    {
        if ($this->steps->count() < $this->requiredStepsCount) {
            return false;
        }

        $filteredSteps = $this->filterSteps();

        return $filteredSteps->count() >= $this->requiredStepsCount;
    }

    private function filterSteps(): Collection
    {
        return $this->steps->filter(function ($step) {
            if ($this->withAttempted) {
                return $step->hasAttempts() || $step->skipped;
            }

            return $step->isPassed() || $step->skipped;
        });
    }
}

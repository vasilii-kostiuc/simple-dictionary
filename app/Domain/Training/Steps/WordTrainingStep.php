<?php

namespace App\Domain\Training\Steps;

use App\Domain\Step\Enums\StepType;

abstract class WordTrainingStep
{
    private readonly StepType $trainingStepType;

    protected int $requiredAnswersCount = 1;

    public function __construct(StepType $trainingStepType)
    {
        $this->trainingStepType = $trainingStepType;
    }

    public function getTrainingStepType(): StepType
    {
        return $this->trainingStepType;
    }

    public function getRequiredAnswersCount(): int
    {
        return $this->requiredAnswersCount;
    }

    public abstract function toArray(): array;
}

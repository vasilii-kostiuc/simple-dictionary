<?php

namespace App\Domain\Training\Steps;

use App\Domain\Training\Enums\TrainingStepType;

abstract class  WordTrainingStep
{
    private readonly TrainingStepType $trainingStepType;

    protected int $requiredAnswersCount = 1;

    public function __construct(TrainingStepType $trainingStepType)
    {
        $this->trainingStepType = $trainingStepType;
    }

    public function getTrainingStepType(): TrainingStepType
    {
        return $this->trainingStepType;
    }

    public function getRequiredAnswersCount(): int
    {
        return $this->requiredAnswersCount;
    }

    public abstract function toArray(): array;


}

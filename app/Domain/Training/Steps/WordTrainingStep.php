<?php

namespace App\Domain\Training\Steps;

use App\Domain\Training\Enums\TrainingStepType;

abstract class  WordTrainingStep
{
    private readonly TrainingStepType $trainingStepType;

    public function __construct(TrainingStepType $trainingStepType)
    {
        $this->trainingStepType = $trainingStepType;
    }

    public function getTrainingStepType(): TrainingStepType
    {
        return $this->trainingStepType;
    }

    public abstract function toArray(): array;
}

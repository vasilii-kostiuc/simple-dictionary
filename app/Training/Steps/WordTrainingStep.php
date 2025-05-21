<?php

namespace App\Training\Steps;

use App\Training\Enums\TrainingStepType;

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

<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Steps\WordTrainingStep;

class SpecificStepTypeTrainingStrategy extends TrainingStrategyAbstract
{
    private TrainingStepType $stepType;

    public function __construct(TrainingStepType $stepType)
    {
        $this->stepType = $stepType;
    }

    public function generateNextStep(): WordTrainingStep
    {
        return $this->trainingStepFactory->createStep($this->training ,$this->stepType);
    }
}

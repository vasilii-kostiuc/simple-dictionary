<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Factories\TrainingStepFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Steps\WordTrainingStep;

class SpecificStepTypeTrainingStrategy extends TrainingStrategyAbstract
{
    private TrainingStepType $stepType;


    public function __construct(Training $training, TrainingStepFactory $trainingStepFactory, TrainingStepType $stepType)
    {
        $this->stepType = $stepType;

        parent::__construct($training, $trainingStepFactory);
    }

    public function generateNextStep(): WordTrainingStep
    {
        return $this->trainingStepFactory->createStep($this->training ,$this->stepType);
    }
}

<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Step\StepFactory;
use App\Domain\Step\Steps\Step;
use App\Domain\Training\Models\Training;

class SpecificStepTypeTrainingStrategy extends TrainingStrategyAbstract
{
    private array $stepTypes;

    public function __construct(Training $training, StepFactory $trainingStepFactory, array $stepTypes)
    {
        $this->stepTypes = $stepTypes;

        parent::__construct($training, $trainingStepFactory);
    }

    public function generateNextStep(): Step
    {
        $stepType = $this->stepTypes[array_rand($this->stepTypes)];
        return $this->trainingStepFactory->createStep($this->training ,$stepType);
    }
}

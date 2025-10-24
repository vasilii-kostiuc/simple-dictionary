<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Factories\TrainingStepFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Steps\WordTrainingStep;

class SpecificStepTypeTrainingStrategy extends TrainingStrategyAbstract
{
    private array $stepTypes;

    public function __construct(Training $training, TrainingStepFactory $trainingStepFactory, array $stepTypes)
    {
        $this->stepTypes = $stepTypes;

        parent::__construct($training, $trainingStepFactory);
    }

    public function generateNextStep(): WordTrainingStep
    {
        $stepType = $this->stepTypes[array_rand($this->stepTypes)];
        return $this->trainingStepFactory->createStep($this->training ,$stepType);
    }
}

<?php

namespace App\Domain\Training\Factories;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Strategies\SpecificStepTypeTrainingStrategy;
use App\Domain\Training\Strategies\TrainingStrategyAbstract;

class TrainingStrategyFactory
{
    private StepFactory $stepFactory;

    public function __construct(StepFactory $stepFactory)
    {
        $this->stepFactory = $stepFactory;
    }

    public function create(Training $training): TrainingStrategyAbstract
    {
         $trainingStrategy = new SpecificStepTypeTrainingStrategy($training, $this->stepFactory,[StepType::ChooseCorrectAnswer, StepType::WriteCorrectAnswer]);

         return $trainingStrategy;
    }
}

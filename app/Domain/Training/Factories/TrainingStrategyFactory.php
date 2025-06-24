<?php

namespace App\Domain\Training\Factories;

use App\Domain\Training\Models\Training;
use App\Domain\Training\Strategies\RandomTrainingStrategy;
use App\Domain\Training\Strategies\TrainingStrategyAbstract;

class TrainingStrategyFactory
{
    private TrainingStepFactory $stepFactory;

    public function __construct(TrainingStepFactory $stepFactory)
    {
        $this->stepFactory = $stepFactory;
    }

    public function create(Training $training): TrainingStrategyAbstract
    {
         $trainingStrategy = new RandomTrainingStrategy($training, $this->stepFactory);

         return $trainingStrategy;
    }
}

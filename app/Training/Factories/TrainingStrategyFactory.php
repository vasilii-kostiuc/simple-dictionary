<?php

namespace App\Training\Factories;

use App\Training\Models\Training;
use App\Training\Strategies\RandomTrainingStrategy;
use App\Training\Strategies\TrainingStrategyAbstract;

class TrainingStrategyFactory
{
    private TrainingStepFactory $stepFactory;

    public function __construct(TrainingStepFactory $stepFactory)
    {
        $this->stepFactory = $stepFactory;
    }

    public function create(Training $training): TrainingStrategyAbstract
    {
         $trainingStrategy = new RandomTrainingStrategy($training->toArray(), $this->stepFactory);

         return $trainingStrategy;
    }
}

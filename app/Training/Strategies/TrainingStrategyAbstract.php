<?php

namespace App\Training\Strategies;

use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\Training;
use App\Training\Steps\WordTrainingStep;

abstract class TrainingStrategyAbstract
{
    protected Training $training;
    protected TrainingStepFactory $trainingStepFactory;

    public function __construct(Training $training, TrainingStepFactory $trainingStepFactory)
    {
        $this->training = $training;
        $this->trainingStepFactory = $trainingStepFactory;
    }

    public abstract function generateNextStep(): WordTrainingStep;
}

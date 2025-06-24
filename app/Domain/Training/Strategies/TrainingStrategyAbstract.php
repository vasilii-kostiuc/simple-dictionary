<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Training\Factories\TrainingStepFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Steps\WordTrainingStep;

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

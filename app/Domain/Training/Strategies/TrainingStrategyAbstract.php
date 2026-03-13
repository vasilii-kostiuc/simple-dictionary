<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Step\StepFactory;
use App\Domain\Step\Steps\Step;
use App\Domain\Training\Models\Training;

abstract class TrainingStrategyAbstract
{
    protected Training $training;
    protected StepFactory $trainingStepFactory;

    public function __construct(Training $training, StepFactory $trainingStepFactory)
    {
        $this->training = $training;
        $this->trainingStepFactory = $trainingStepFactory;
    }

    public abstract function generateNextStep(): Step;
}

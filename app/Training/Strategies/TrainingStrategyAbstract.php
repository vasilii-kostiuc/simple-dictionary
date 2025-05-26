<?php

namespace App\Training\Strategies;

use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\TrainingStep;

abstract class TrainingStrategyAbstract
{
    protected array $training;
    protected TrainingStepFactory $trainingStepFactory;

    public function __construct(array $training, TrainingStepFactory $trainingStepFactory)
    {
        $this->training = $training;
        $this->trainingStepFactory = $trainingStepFactory;
    }

    public abstract function generateNextStep(): ?TrainingStep;
}

<?php

namespace App\Training\Strategies;

use App\Training\Enums\TrainingStepType;
use App\Training\Steps\WordTrainingStep;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): WordTrainingStep
    {
        $stepType = TrainingStepType::getRandomInstance();

        return $this->trainingStepFactory->create($this->training ,$stepType);
    }
}

<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\Steps\Step;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): Step
    {
        $stepType = StepType::getRandomInstance();

        return $this->trainingStepFactory->createStep($this->training ,$stepType);
    }
}

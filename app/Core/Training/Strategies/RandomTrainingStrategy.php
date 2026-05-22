<?php

namespace App\Core\Training\Strategies;

use App\Core\Step\Enums\StepType;
use App\Core\Step\Steps\Step;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): Step
    {
        $stepType = StepType::getRandomInstance();

        return $this->trainingStepFactory->createStep($stepType, $this->wordsProvider);
    }
}

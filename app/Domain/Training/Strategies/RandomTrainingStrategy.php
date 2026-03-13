<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Step\Enums\StepType;
use App\Domain\Training\Steps\WordTrainingStep;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): WordTrainingStep
    {
        $stepType = StepType::getRandomInstance();

        return $this->trainingStepFactory->createStep($this->training ,$stepType);
    }
}

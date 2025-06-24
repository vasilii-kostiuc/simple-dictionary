<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Steps\WordTrainingStep;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): WordTrainingStep
    {
        $stepType = TrainingStepType::getRandomInstance();

        return $this->trainingStepFactory->create($this->training ,$stepType);
    }
}

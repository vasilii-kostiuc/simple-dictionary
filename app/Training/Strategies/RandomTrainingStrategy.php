<?php

namespace App\Training\Strategies;

use App\Models\Training\TrainingStep;
use App\Training\Enums\TrainingStepType;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): ?TrainingStep
    {
        $stepType = TrainingStepType::getRandomInstance();

        return $this->trainingStepFactory->create($stepType);
    }
}

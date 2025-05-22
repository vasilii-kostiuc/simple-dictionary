<?php

namespace App\Training\Strategies;

use App\Training\Enums\TrainingStepType;
use App\Training\Models\TrainingStep;

class RandomTrainingStrategy extends TrainingStrategyAbstract
{
    public function generateNextStep(): ?TrainingStep
    {
        $stepType = TrainingStepType::getRandomInstance();

        return $this->trainingStepFactory->create($stepType);
    }
}

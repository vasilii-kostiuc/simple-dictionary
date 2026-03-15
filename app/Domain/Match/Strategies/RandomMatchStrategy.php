<?php

namespace App\Domain\Match\Strategies;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\Steps\Step;

class RandomMatchStrategy extends MatchStrategyAbstract
{
    public function generateNextStep(): Step
    {
        $stepTypes = [
            StepType::ChooseCorrectAnswer,
            StepType::WriteCorrectAnswer,
            StepType::EstablishCompliance,
        ];

        $randomStepType = $stepTypes[array_rand($stepTypes)];

        return $this->stepFactory->createStep($randomStepType, $this->wordsProvider);
    }
}

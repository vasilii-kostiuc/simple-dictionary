<?php

namespace App\Core\Match\Strategies;

use App\Core\Step\Enums\StepType;
use App\Core\Step\Steps\Step;

class RandomMatchStrategy extends MatchStrategyAbstract
{
    public function generateNextStep(): Step
    {
        $stepTypes = [
            StepType::ChooseCorrectAnswer,
            StepType::WriteCorrectAnswer,
            //            StepType::EstablishCompliance,
        ];

        $randomStepType = $stepTypes[array_rand($stepTypes)];

        return $this->stepFactory->createStep($randomStepType, $this->wordsProvider);
    }
}

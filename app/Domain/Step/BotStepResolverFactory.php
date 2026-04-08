<?php

namespace App\Domain\Step;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepResolvers\BotResolver;
use App\Domain\Step\StepResolvers\ChooseCorrectAnswerResolver;
use App\Domain\Step\StepResolvers\EstablishComplianceResolver;
use App\Domain\Step\StepResolvers\StepResolverInterface;
use App\Domain\Step\StepResolvers\WriteAnswerResolver;

class BotStepResolverFactory
{
    public function __construct(
        private float $accuracy = 0.8
    ) {}

    public function create(StepType $stepType): StepResolverInterface
    {
        $inner = match ($stepType) {
            StepType::ChooseCorrectAnswer => new ChooseCorrectAnswerResolver(),
            StepType::WriteCorrectAnswer  => new WriteAnswerResolver(),
            StepType::EstablishCompliance => new EstablishComplianceResolver(),
        };

        return new BotResolver($inner, $this->accuracy);
    }
}

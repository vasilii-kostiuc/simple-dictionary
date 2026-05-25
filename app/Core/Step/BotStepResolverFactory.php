<?php

namespace App\Core\Step;

use App\Core\Step\Enums\StepType;
use App\Core\Step\StepResolvers\BotResolver;
use App\Core\Step\StepResolvers\ChooseCorrectAnswerResolver;
use App\Core\Step\StepResolvers\EstablishComplianceResolver;
use App\Core\Step\StepResolvers\StepResolverInterface;
use App\Core\Step\StepResolvers\WriteAnswerResolver;

class BotStepResolverFactory
{
    public function __construct(
        private float $accuracy = 0.8
    ) {}

    public function create(StepType $stepType): StepResolverInterface
    {
        $inner = match ($stepType) {
            StepType::ChooseCorrectAnswer => new ChooseCorrectAnswerResolver,
            StepType::WriteCorrectAnswer => new WriteAnswerResolver,
            StepType::EstablishCompliance => new EstablishComplianceResolver,
        };

        return new BotResolver($inner, $this->accuracy);
    }
}

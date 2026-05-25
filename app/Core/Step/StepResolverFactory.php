<?php

namespace App\Core\Step;

use App\Core\Step\Enums\StepType;
use App\Core\Step\StepResolvers\ChooseCorrectAnswerResolver;
use App\Core\Step\StepResolvers\EstablishComplianceResolver;
use App\Core\Step\StepResolvers\WriteAnswerResolver;

class StepResolverFactory
{
    public function create(StepType $stepType)
    {
        return match ($stepType) {
            StepType::ChooseCorrectAnswer => new ChooseCorrectAnswerResolver,
            StepType::WriteCorrectAnswer => new WriteAnswerResolver,
            StepType::EstablishCompliance => new EstablishComplianceResolver,
            default => new EstablishComplianceResolver,
        };
    }
}

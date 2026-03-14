<?php

namespace App\Domain\Training\Factories;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepResolvers\ChooseCorrectAnswerResolver;
use App\Domain\Step\StepResolvers\EstablishComplianceResolver;
use App\Domain\Step\StepResolvers\WriteAnswerResolver;

class StepResolverFactory
{
    public function create(StepType $stepType){
        return match($stepType){
            StepType::ChooseCorrectAnswer => new ChooseCorrectAnswerResolver(),
            StepType::WriteCorrectAnswer => new WriteAnswerResolver(),
            StepType::EstablishCompliance => new EstablishComplianceResolver(),
            default => new EstablishComplianceResolver(),
        };
    }

}

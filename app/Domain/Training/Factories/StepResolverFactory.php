<?php

namespace App\Domain\Training\Factories;

use App\Domain\Step\Enums\StepType;
use App\Domain\Training\StepResolvers\ChooseCorrectAnswerResolver;
use App\Domain\Training\StepResolvers\EstablishComplianceResolver;
use App\Domain\Training\StepResolvers\WriteAnswerResolver;

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

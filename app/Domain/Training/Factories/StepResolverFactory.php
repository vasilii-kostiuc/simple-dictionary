<?php

namespace App\Domain\Training\Factories;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\StepResolvers\ChooseCorrectAnswerResolver;
use App\Domain\Training\StepResolvers\EstablishComplianceResolver;
use App\Domain\Training\StepResolvers\WriteAnswerResolver;

class StepResolverFactory
{
    public function create(TrainingStepType $stepType){
        return match($stepType){
            //TrainingStepType::ChooseCorrectAnswer => new ChooseCorrectAnswerResolver(),
            //TrainingStepType::WriteCorrectAnswer => new WriteAnswerResolver(),
            //TrainingStepType::EstablishCompliance => new EstablishComplianceResolver(),
            default => new EstablishComplianceResolver(),
        };
    }

}

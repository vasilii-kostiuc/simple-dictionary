<?php

namespace App\Domain\Training\Factories;

use App\Domain\Training\Enums\TrainingStepType;

class StepResolverFactory
{
    public function create(TrainingStepType $stepType){
        return match($stepType){
            default => throw new \Exception('Not implemented'),
        };
    }

}

<?php

namespace App\Training\Factories;

use App\Training\CompletionConditions\CompletionConditionInterface;
use App\Training\CompletionConditions\StepsCompletionCondition;
use App\Training\CompletionConditions\TimeCompletionCondition;
use App\Training\CompletionConditions\UnlimitedCompletionCondition;
use App\Training\Enums\TrainingCompletionType;
use App\Training\Models\Training;

class CompletionConditionFactory
{
    public function create(Training $training): CompletionConditionInterface
    {
        return match ($training->completion_type) {
            TrainingCompletionType::Time => new TimeCompletionCondition($training->completion_type_params->duration, $training->started_at),
            TrainingCompletionType::Steps => new StepsCompletionCondition($training->completion_type_params->steps_count, $training->steps),
            TrainingCompletionType::Unlimited => new UnlimitedCompletionCondition(),
            default => throw new \Exception('Completion condition not found'),
        };
    }
}

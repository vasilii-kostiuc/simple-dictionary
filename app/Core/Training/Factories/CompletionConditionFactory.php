<?php

namespace App\Core\Training\Factories;

use App\Core\Shared\CompletionConditions\CompletionConditionInterface;
use App\Core\Shared\CompletionConditions\StepsCompletionCondition;
use App\Core\Shared\CompletionConditions\TimeCompletionCondition;
use App\Core\Shared\CompletionConditions\UnlimitedCompletionCondition;
use App\Core\Training\Enums\TrainingCompletionType;
use App\Core\Training\Models\Training;

class CompletionConditionFactory
{
    public function create(Training $training): CompletionConditionInterface
    {
        // dd($training->completion_type);
        $completionType = $training->completion_type;

        return match ($completionType) {
            TrainingCompletionType::Time => new TimeCompletionCondition($training->completion_type_params['duration'], $training->started_at),
            TrainingCompletionType::Steps => new StepsCompletionCondition($training->completion_type_params['steps_count'], $training->steps),
            TrainingCompletionType::Unlimited => new UnlimitedCompletionCondition,
            default => new StepsCompletionCondition($training->completion_type_params['steps_count'], $training->steps),
        };
    }
}

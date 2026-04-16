<?php

namespace App\Domain\Training\Factories;

use App\Domain\Shared\CompletionConditions\CompletionConditionInterface;
use App\Domain\Shared\CompletionConditions\StepsCompletionCondition;
use App\Domain\Shared\CompletionConditions\TimeCompletionCondition;
use App\Domain\Shared\CompletionConditions\UnlimitedCompletionCondition;
use App\Domain\Training\Enums\TrainingCompletionType;
use App\Domain\Training\Models\Training;

class CompletionConditionFactory
{
    public function create(Training $training): CompletionConditionInterface
    {
        //dd($training->completion_type);
        $completionType = $training->completion_type;

        return match ($completionType) {
            TrainingCompletionType::Time => new TimeCompletionCondition($training->completion_type_params['duration'], $training->started_at),
            TrainingCompletionType::Steps => new StepsCompletionCondition($training->completion_type_params['steps_count'], $training->steps),
            TrainingCompletionType::Unlimited => new UnlimitedCompletionCondition(),
            default => new StepsCompletionCondition($training->completion_type_params['steps_count'], $training->steps),
        };
    }
}

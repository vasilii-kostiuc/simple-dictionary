<?php

namespace App\Domain\Training\Factories;

use App\Domain\Training\CompletionConditions\CompletionConditionInterface;
use App\Domain\Training\CompletionConditions\StepsCompletionCondition;
use App\Domain\Training\CompletionConditions\TimeCompletionCondition;
use App\Domain\Training\CompletionConditions\UnlimitedCompletionCondition;
use App\Domain\Training\Enums\TrainingCompletionType;
use App\Domain\Training\Models\Training;

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

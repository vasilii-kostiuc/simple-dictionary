<?php

namespace App\Training\CompletionConditions;

use App\Training\CompletionConditions\CompletionConditionInterface;

/**
 *  Null Object Pattern implementation for CompletionConditionInterface
 *  Represents a training that has no completion conditions and is always considered completed
 */
class UnlimitedCompletionCondition implements CompletionConditionInterface
{
    public function isCompleted(): bool
    {
        return true;
    }
}

<?php

namespace App\Domain\Shared\CompletionConditions;

/**
 *  Null Object Pattern implementation for CompletionConditionInterface
 *  Represents a training that has no completion conditions and is always considered completed
 */
class UnlimitedCompletionCondition implements CompletionConditionInterface
{
    public function isCompleted(): bool
    {
        return false;
    }
}

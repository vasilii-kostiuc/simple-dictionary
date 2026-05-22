<?php

namespace App\Core\Shared\CompletionConditions;

interface CompletionConditionInterface
{
    public function isCompleted(): bool;
}

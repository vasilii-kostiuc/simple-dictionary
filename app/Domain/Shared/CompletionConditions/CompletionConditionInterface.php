<?php

namespace App\Domain\Shared\CompletionConditions;

interface CompletionConditionInterface
{
    public function isCompleted(): bool;
}

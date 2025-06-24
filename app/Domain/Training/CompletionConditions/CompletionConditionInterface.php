<?php

namespace App\Domain\Training\CompletionConditions;

interface CompletionConditionInterface
{
    public function isCompleted(): bool;
}

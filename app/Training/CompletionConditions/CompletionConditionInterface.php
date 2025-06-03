<?php

namespace App\Training\CompletionConditions;

interface CompletionConditionInterface
{
    public function isCompleted(): bool;
}

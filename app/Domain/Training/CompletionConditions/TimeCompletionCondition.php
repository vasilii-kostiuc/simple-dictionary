<?php

namespace App\Domain\Training\CompletionConditions;

use Carbon\Carbon;
use Carbon\Traits\Timestamp;

class TimeCompletionCondition implements CompletionConditionInterface
{
    protected readonly int $duration;
    protected readonly string $startedAt;

    public function __construct(int $duration, string $startedAt)
    {
        $this->duration = $duration;
        $this->startedAt = $startedAt;
    }

    public function isCompleted(): bool
    {
        $startedTimestamp = Carbon::parse($this->startedAt);
        if ($startedTimestamp->diffInSeconds(Carbon::now()) > $this->duration * 60) {
            return true;
        }

        return false;
    }
}

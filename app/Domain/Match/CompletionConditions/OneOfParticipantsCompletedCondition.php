<?php

namespace App\Domain\Match\CompletionConditions;

use App\Domain\Shared\CompletionConditions\CompletionConditionInterface;
use Illuminate\Support\Collection;

class OneOfParticipantsCompletedCondition implements CompletionConditionInterface
{
    public function __construct(
        private readonly Collection $matchUsers,
    ) {
    }

    public function isCompleted(): bool
    {
        return $this->matchUsers->some(fn ($matchUser) => $matchUser->status->isTerminal());
    }
}

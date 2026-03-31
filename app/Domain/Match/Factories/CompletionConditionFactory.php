<?php

namespace App\Domain\Match\Factories;

use App\Domain\Match\CompletionConditions\AllParticipantsCompletedCondition;
use App\Domain\Match\CompletionConditions\OneOfParticipantsCompletedCondition;
use App\Domain\Match\Enums\MatchType;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Shared\CompletionConditions\CompletionConditionInterface;
use App\Domain\Shared\CompletionConditions\TimeCompletionCondition;

class CompletionConditionFactory
{
    public function create(MatchModel $match): CompletionConditionInterface
    {
        return match ($match->match_type) {
            MatchType::Time => new TimeCompletionCondition(
                $match->match_type_params['duration'],
                $match->started_at->toDateTimeString()
            ),
            MatchType::Steps => new AllParticipantsCompletedCondition(
                $match->matchUsers
            ),
            MatchType::Race => new OneOfParticipantsCompletedCondition(
                $match->matchUsers
            ),
        };
    }
}

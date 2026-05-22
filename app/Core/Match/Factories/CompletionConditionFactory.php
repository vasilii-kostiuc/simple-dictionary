<?php

namespace App\Core\Match\Factories;

use App\Core\Match\CompletionConditions\AllParticipantsCompletedCondition;
use App\Core\Match\CompletionConditions\OneOfParticipantsCompletedCondition;
use App\Core\Match\Enums\MatchType;
use App\Core\Match\Models\MatchModel;
use App\Core\Shared\CompletionConditions\CompletionConditionInterface;
use App\Core\Shared\CompletionConditions\TimeCompletionCondition;

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

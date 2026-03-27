<?php

namespace App\Domain\Match\Service;

use App\Domain\Match\Models\MatchModel;

class MatchSummaryBuilder
{
    public function build(MatchModel $match): object
    {
        $match->loadMissing('matchUsers');
        $match->matchUsers->each(fn ($mu) => $mu->setRelation('match', $match));

        $matchTime = $match->completed_at
            ? $match->started_at->diffInSeconds($match->completed_at)
            : null;

        $participants = $match->matchUsers;

        $winner = $match->matchUsers->where('is_winner', true)->first();

        return (object) [
            'match_id' => $match->id,
            'match_time_seconds' => $matchTime,
            'participants' => $participants,
            'winner' => $winner ? [
                'participant_name' => $winner->participant_name,
                'user_id' => $winner->user_id,
                'guest_id' => $winner->guest_id,
                'is_guest' => $winner->isGuest(),
                'score' => $winner->score,
            ] : null,
            'completion_reason' => $match->completion_reason?->value,
            'started_at' => $match->started_at,
            'completed_at' => $match->completed_at,
        ];
    }
}

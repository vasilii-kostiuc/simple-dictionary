<?php

namespace App\Domain\Match\Service;

use App\Domain\Match\Models\MatchModel;

class MatchSummaryBuilder
{
    public function build(MatchModel $match): object
    {
        $match->loadMissing('matchUsers');

        $matchTime = $match->completed_at
            ? $match->started_at->diffInSeconds($match->completed_at)
            : null;

        $participants = $match->matchUsers->map(function ($matchUser) use ($match) {
            $stepsCount = $match->steps()
                ->where(function ($q) use ($matchUser) {
                    if ($matchUser->user_id) {
                        $q->where('user_id', $matchUser->user_id);
                    } else {
                        $q->where('guest_id', $matchUser->guest_id);
                    }
                })
                ->count();

            return [
                'user_id' => $matchUser->user_id,
                'guest_id' => $matchUser->guest_id,
                'is_guest' => $matchUser->isGuest(),
                'participant_name' => $matchUser->participant_name,
                'participant_avatar' => $matchUser->participant_avatar,
                'score' => $matchUser->score,
                'answered_count' => $matchUser->answered_count,
                'correct_answers_count' => $matchUser->correct_answers_count,
                'steps_count' => $stepsCount,
                'place' => $matchUser->place,
                'is_winner' => $matchUser->is_winner,
            ];
        });

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

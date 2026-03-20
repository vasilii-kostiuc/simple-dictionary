<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'guest_id' => $this->guest_id,
            'participant_name' => $this->participant_name,
            'participant_avatar' => $this->participant_avatar,
            'score' => $this->score,
            'answered_count' => $this->answered_count,
            'correct_answers_count' => $this->correct_answers_count,
            'place' => $this->place,
            'is_winner' => $this->is_winner,
            'is_guest' => $this->isGuest(),
        ];
    }
}

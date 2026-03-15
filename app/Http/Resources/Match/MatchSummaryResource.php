<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'match_id' => $this->match_id,
            'match_time_seconds' => $this->match_time_seconds,
            'participants' => $this->participants,
            'winner' => $this->winner,
            'completion_reason' => $this->completion_reason,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
        ];
    }
}

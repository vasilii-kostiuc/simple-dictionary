<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dictionary_id' => $this->dictionary_id,
            'match_type' => $this->match_type,
            'match_type_params' => $this->match_type_params,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'completion_reason' => $this->completion_reason,
            'participants' => MatchUserResource::collection($this->whenLoaded('matchUsers')),
            'time_left' => $this->when(
                $this->started_at && !$this->completed_at,
                function () {
                    $elapsed = now()->diffInSeconds($this->started_at);
                    $duration = $this->match_type_params['duration'] ?? 0;
                    return max(0, $duration - $elapsed);
                }
            ),
        ];
    }
}

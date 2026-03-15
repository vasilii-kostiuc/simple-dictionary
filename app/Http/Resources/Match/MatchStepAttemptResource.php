<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchStepAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_step_id' => $this->match_step_id,
            'attempt_number' => $this->attempt_number,
            'sub_index' => $this->sub_index,
            'attempt_data' => $this->attempt_data,
            'is_correct' => $this->is_correct,
            'created_at' => $this->created_at,
        ];
    }
}

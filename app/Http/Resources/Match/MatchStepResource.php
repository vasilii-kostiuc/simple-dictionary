<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'step_number' => $this->step_number,
            'step_type_id' => $this->step_type_id,
            'step_data' => $this->step_data,
            'required_answers_count' => $this->required_answers_count,
            'skipped' => $this->skipped,
            'is_passed' => $this->isPassed(),
        ];
    }
}

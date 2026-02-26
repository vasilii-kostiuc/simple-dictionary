<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainingSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'training_time_seconds' => $this->training_time_seconds,
            'steps_count' => $this->steps_count,
            'correct_answers_count' => $this->correct_answers_count,
            'skipped_steps_count' => $this->skipped_steps_count,
            'completion_reason' => $this->completion_reason,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
        ];
    }
}

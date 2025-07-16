<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingStepAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'training_step_id' => $this->training_step_id,
            'is_correct' => $this->is_correct,
            'attempt_data' => $this->attempt_data,
            'attempt_number' => $this->attempt_number,
            'sub_index' =>$this->sub_index,
        ];
    }
}

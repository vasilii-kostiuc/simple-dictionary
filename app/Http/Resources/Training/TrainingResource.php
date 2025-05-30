<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    /**
     * Transform the resource int
     *
     o an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dictionary_id' => $this->dictionary_id,
            'training_type_id' => $this->training_type_id,
            'status' => $this->status,
            'completion_type' => $this->completion_type,
            'completion_type_params' => $this->completion_type_params,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
        ];
    }
}

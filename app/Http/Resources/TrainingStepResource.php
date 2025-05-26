<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingStepResource extends JsonResource
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
            'step_number' => $this->step_number,
            'training_id' => $this->training_id,
            'step_type_id' => $this->step_type_id,
            'step_data' => $this->step_data,
        ];
    }
}

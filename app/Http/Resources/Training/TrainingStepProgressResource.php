<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingStepProgressResource extends JsonResource
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
            'required_answers_count' => $this->required_answers_count,
            'answered' => $this->answered,
            'is_passed' => $this->is_passed,
            'skipped' => $this->skipped,
            'skipped_at' => $this->skipped_at,
        ];
    }
}

<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TrainingStepAttempt',
    description: 'Training step attempt resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'training_step_id', type: 'integer', example: 1),
        new OA\Property(property: 'is_correct', type: 'boolean', example: true),
        new OA\Property(property: 'attempt_data', type: 'object', description: 'Submitted answer data', example: '{"answer": "Привет"}'),
        new OA\Property(property: 'attempt_number', type: 'integer', example: 1),
        new OA\Property(property: 'sub_index', type: 'integer', nullable: true, example: null),
    ]
)]
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
            'sub_index' => $this->sub_index,
        ];
    }
}

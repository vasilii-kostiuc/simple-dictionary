<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TrainingStep',
    description: 'Training step resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'step_number', type: 'integer', example: 1),
        new OA\Property(property: 'training_id', type: 'integer', example: 1),
        new OA\Property(property: 'step_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'step_data', type: 'object', description: 'Step question data (varies by step type)', example: '{"question": "Hello", "options": ["Привет", "Пока", "Да"]}'),
    ]
)]
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

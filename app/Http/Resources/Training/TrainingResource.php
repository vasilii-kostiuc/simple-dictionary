<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Training',
    description: 'Training resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'dictionary_id', type: 'integer', example: 1),
        new OA\Property(property: 'training_type_id', type: 'integer', description: '1=TopWords, 2=MyWords, 3=AllWords', example: 1),
        new OA\Property(property: 'status', type: 'integer', description: '1=New, 2=InProgress, 3=Completed', example: 1),
        new OA\Property(property: 'completion_type', type: 'string', enum: ['time', 'steps', 'unlimited'], example: 'steps'),
        new OA\Property(property: 'completion_type_params', type: 'object', nullable: true, example: '{"steps": 10}'),
        new OA\Property(property: 'started_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:00:00Z'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: null),
    ]
)]
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

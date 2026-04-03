<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TrainingSummary',
    description: 'Training summary resource',
    properties: [
        new OA\Property(property: 'training_time_seconds', type: 'integer', nullable: true, example: 120),
        new OA\Property(property: 'steps_count', type: 'integer', example: 10),
        new OA\Property(property: 'correct_answers_count', type: 'integer', example: 8),
        new OA\Property(property: 'skipped_steps_count', type: 'integer', example: 1),
        new OA\Property(property: 'completion_reason', type: 'string', nullable: true, enum: ['Expired', 'Leaved', 'AllStepsCompleted', 'Terminated'], example: 'AllStepsCompleted'),
        new OA\Property(property: 'started_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:00:00Z'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:02:00Z'),
    ]
)]
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

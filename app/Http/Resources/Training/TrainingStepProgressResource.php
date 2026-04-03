<?php

namespace App\Http\Resources\Training;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TrainingStepProgress',
    description: 'Training step progress resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'required_answers_count', type: 'integer', example: 3),
        new OA\Property(property: 'answered', type: 'integer', description: 'Number of correct answers given', example: 1),
        new OA\Property(property: 'is_passed', type: 'boolean', example: false),
        new OA\Property(property: 'skipped', type: 'boolean', example: false),
        new OA\Property(property: 'skipped_at', type: 'string', format: 'date-time', nullable: true, example: null),
    ]
)]
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

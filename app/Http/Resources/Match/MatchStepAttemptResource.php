<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchStepAttempt',
    description: 'Match step attempt resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'match_step_id', type: 'integer', example: 1),
        new OA\Property(property: 'attempt_number', type: 'integer', example: 1),
        new OA\Property(property: 'sub_index', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'attempt_data', type: 'object', description: 'Submitted answer data', example: '{"answer": "Привет"}'),
        new OA\Property(property: 'is_correct', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-01-01T12:00:00Z'),
    ]
)]
class MatchStepAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_step_id' => $this->match_step_id,
            'attempt_number' => $this->attempt_number,
            'sub_index' => $this->sub_index,
            'attempt_data' => $this->attempt_data,
            'is_correct' => $this->is_correct,
            'created_at' => $this->created_at,
        ];
    }
}

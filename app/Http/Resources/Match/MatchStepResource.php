<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchStep',
    description: 'Match step resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'match_id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 42),
        new OA\Property(property: 'guest_id', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'step_number', type: 'integer', example: 1),
        new OA\Property(property: 'step_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'step_data', type: 'object', description: 'Step question data', example: '{"question": "Hello", "options": ["Привет", "Пока"]}'),
        new OA\Property(property: 'required_answers_count', type: 'integer', example: 1),
        new OA\Property(property: 'skipped', type: 'boolean', example: false),
        new OA\Property(property: 'is_passed', type: 'boolean', example: false),
    ]
)]
class MatchStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'user_id' => $this->user_id,
            'guest_id' => $this->guest_id,
            'step_number' => $this->step_number,
            'step_type_id' => $this->step_type_id,
            'step_data' => $this->step_data,
            'required_answers_count' => $this->required_answers_count,
            'skipped' => $this->skipped,
            'is_passed' => $this->isPassed(),
        ];
    }
}

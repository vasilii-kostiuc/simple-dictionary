<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Match',
    description: 'Match resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'language_from_id', type: 'integer', example: 1),
        new OA\Property(property: 'language_to_id', type: 'integer', example: 2),
        new OA\Property(property: 'dictionary_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'match_type', type: 'string', enum: ['time', 'steps'], example: 'time'),
        new OA\Property(property: 'match_type_params', type: 'object', example: '{"duration": 120}'),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'completed', 'cancelled'], example: 'in_progress'),
        new OA\Property(property: 'started_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:00:00Z'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: null),
        new OA\Property(property: 'completion_reason', type: 'string', nullable: true, enum: ['time_expired', 'steps_completed', 'not_held', 'no_activity', 'all_players_left', 'forfeited', 'cancelled'], example: null),
        new OA\Property(property: 'participants', type: 'array', items: new OA\Items(ref: '#/components/schemas/MatchUser')),
        new OA\Property(property: 'time_left', type: 'integer', nullable: true, description: 'Seconds remaining (only during active time match)', example: 60),
    ]
)]
class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language_from_id' => $this->language_from_id,
            'language_to_id' => $this->language_to_id,
            'dictionary_id' => $this->dictionary_id,
            'match_type' => $this->match_type,
            'match_type_params' => $this->match_type_params,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'completion_reason' => $this->completion_reason,
            'participants' => MatchUserResource::collection($this->matchUsers),
            'time_left' => $this->when(
                $this->started_at && ! $this->completed_at,
                function () {
                    $elapsed = now()->diffInSeconds($this->started_at);
                    $duration = $this->match_type_params['duration'] ?? 0;
                    return max(0, $duration - $elapsed);
                }
            ),
        ];
    }
}

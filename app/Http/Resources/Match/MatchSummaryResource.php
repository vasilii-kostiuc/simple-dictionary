<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchSummary',
    description: 'Match summary resource',
    properties: [
        new OA\Property(property: 'match_id', type: 'integer', example: 1),
        new OA\Property(property: 'match_time_seconds', type: 'integer', nullable: true, example: 120),
        new OA\Property(property: 'participants', type: 'array', items: new OA\Items(ref: '#/components/schemas/MatchUser')),
        new OA\Property(property: 'winner', ref: '#/components/schemas/MatchUser', type: 'object', nullable: true),
        new OA\Property(property: 'completion_reason', type: 'string', nullable: true, enum: ['time_expired', 'steps_completed', 'not_held', 'no_activity', 'all_players_left', 'forfeited', 'cancelled'], example: 'time_expired'),
        new OA\Property(property: 'started_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:00:00Z'),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true, example: '2026-01-01T12:02:00Z'),
    ]
)]
class MatchSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'match_id' => $this->match_id,
            'match_time_seconds' => $this->match_time_seconds,
            'participants' => MatchUserResource::collection($this->participants),
            'winner' => $this->winner,
            'completion_reason' => $this->completion_reason,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
        ];
    }
}

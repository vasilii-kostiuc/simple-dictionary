<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchUser',
    description: 'Match participant resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 42),
        new OA\Property(property: 'guest_id', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'participant_name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'participant_avatar', type: 'string', nullable: true, example: 'https://example.com/avatar.jpg'),
        new OA\Property(property: 'score', type: 'integer', example: 100),
        new OA\Property(property: 'answered_count', type: 'integer', example: 5),
        new OA\Property(property: 'correct_answers_count', type: 'integer', example: 4),
        new OA\Property(property: 'place', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'is_winner', type: 'boolean', example: true),
        new OA\Property(property: 'is_guest', type: 'boolean', example: false),
        new OA\Property(property: 'steps_count', type: 'integer', example: 5),
        new OA\Property(property: 'skipped_steps_count', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'finished', 'spectating', 'left', 'disconnected', 'forfeited'], example: 'active'),
    ]
)]
class MatchUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'guest_id' => $this->guest_id,
            'participant_name' => $this->participant_name,
            'participant_avatar' => $this->resolveParticipantAvatar(),
            'score' => $this->score,
            'answered_count' => $this->answered_count,
            'correct_answers_count' => $this->correct_answers_count,
            'place' => $this->place,
            'is_winner' => $this->is_winner,
            'is_guest' => $this->isGuest(),
            'steps_count' => $this->stepsCount(),
            'skipped_steps_count' => $this->skippedStepsCount(),
            'status' => $this->status,
        ];
    }

    private function resolveParticipantAvatar(): ?string
    {
        if (blank($this->participant_avatar)) {
            return null;
        }

        if ($this->isGuest()) {
            return $this->participant_avatar;
        }

        if (Str::startsWith($this->participant_avatar, ['http://', 'https://'])) {
            return $this->participant_avatar;
        }

        return Storage::disk('public')->url($this->participant_avatar);
    }
}

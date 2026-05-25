<?php

namespace App\Core\Match\DTO;

use App\Core\Match\Models\MatchStep;
use App\Core\Match\Models\MatchUser;

readonly class ParticipantIdentifier
{
    private function __construct(
        public ?int $userId,
        public ?string $guestId,
    ) {}

    public static function forUser(int $userId): self
    {
        return new self($userId, null);
    }

    public static function forGuest(string $guestId): self
    {
        return new self(null, $guestId);
    }

    public static function fromMatchStep(MatchStep $step): self
    {
        return new self($step->user_id, $step->guest_id);
    }

    public static function fromMatchUser(MatchUser $matchUser): self
    {
        return new self($matchUser->user_id, $matchUser->guest_id);
    }
}

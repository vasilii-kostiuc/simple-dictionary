<?php

namespace App\Domain\Match\Events;

use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchUserAnsweredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MatchModel $match,
        public int|string $participantId, // user_id or guest_id
        public bool $isCorrect,
        public MatchUser $matchUser
    ) {}
}

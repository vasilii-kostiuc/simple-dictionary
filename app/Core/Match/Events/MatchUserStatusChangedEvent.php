<?php

namespace App\Core\Match\Events;

use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchUserStatusChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MatchModel $match,
        public MatchUser $matchUser,
    ) {}
}

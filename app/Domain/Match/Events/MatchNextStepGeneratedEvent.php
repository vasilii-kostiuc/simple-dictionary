<?php

namespace App\Domain\Match\Events;

use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchNextStepGeneratedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MatchModel $match,
        public MatchStep $nextStep
    ) {
    }
}


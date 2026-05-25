<?php

namespace App\Core\Match\Events;

use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchStep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchNextStepGeneratedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MatchModel $match,
        public MatchStep $nextStep
    ) {}
}

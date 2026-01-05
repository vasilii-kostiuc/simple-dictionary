<?php

namespace App\Domain\Training\Events;

use App\Domain\Training\Models\Training;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingStartedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Training $training;

    /**
     * Create a new event instance.
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }
}

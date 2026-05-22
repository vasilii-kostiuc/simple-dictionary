<?php

namespace App\Core\Training\Events;

use App\Core\Training\Models\Training;
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

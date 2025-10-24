<?php

namespace App\Domain\Training\Events;

use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepSkippedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Training $training;
    public TrainingStep $step;

    /**
     * Create a new event instance.
     */
    public function __construct(Training $training, TrainingStep $step)
    {
        $this->training = $training;
        $this->step = $step;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

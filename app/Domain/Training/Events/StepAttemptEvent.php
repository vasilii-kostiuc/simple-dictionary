<?php

namespace App\Domain\Training\Events;

use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStepAttempt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepAttemptEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Training $training;
    public TrainingStepAttempt $attempt;

    /**
     * Create a new event instance.
     */
    public function __construct(Training $training, TrainingStepAttempt $stepAttempt)
    {

        $this->training = $training;
        $this->attempt = $stepAttempt;
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

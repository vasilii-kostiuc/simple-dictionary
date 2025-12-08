<?php

namespace App\Domain\Training\Events;

use App\Domain\Training\Models\Training;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

class TrainingCompleted
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

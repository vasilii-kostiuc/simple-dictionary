<?php

namespace App\Domain\Training\Listeners;

use App\Domain\Training\Events\TrainingCompleted;
use App\Domain\Training\Events\TrainingStartedEvent;
use App\Domain\Training\Factories\CompletionConditionFactory;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

class TrainingStartedListener
{
    private MessageBrokerFactory $messageBrokerFactory;

    /**
     * Create the event listener.
     */
    public function __construct(MessageBrokerFactory $messageBrokerFactory)
    {
        $this->messageBrokerFactory = $messageBrokerFactory;
    }

    /**
     * Handle the event.
     */
    public function handle(TrainingStartedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'training_started',
            'data' => [
                'training_id' => $event->training->id,
                'completion_type' => $event->training->completion_type,
                'completion_type_params' => $event->training->completion_type_params,
                'started_at' => $event->training->started_at,
            ]
        ];

        $messageBroker->publish('training', json_encode($payload));
    }
}

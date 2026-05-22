<?php

namespace App\Core\Training\Listeners;

use App\Core\Training\Events\TrainingStartedEvent;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class TrainingStartedListener
{
    private BrokerFactory $messageBrokerFactory;

    /**
     * Create the event listener.
     */
    public function __construct(BrokerFactory $messageBrokerFactory)
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
            ],
        ];

        $messageBroker->publish('api.training', json_encode($payload));
    }
}

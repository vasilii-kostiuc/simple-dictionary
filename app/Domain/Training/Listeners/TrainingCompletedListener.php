<?php

namespace App\Domain\Training\Listeners;

use App\Domain\Training\Events\TrainingCompleted;
use App\Domain\Training\Factories\CompletionConditionFactory;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

class TrainingCompletedListener
{
    private CompletionConditionFactory $completionConditionFactory;
    private MessageBrokerFactory $messageBrokerFactory;

    /**
     * Create the event listener.
     */
    public function __construct(CompletionConditionFactory $completionConditionFactory, MessageBrokerFactory $messageBrokerFactory)
    {
        $this->completionConditionFactory = $completionConditionFactory;
        $this->messageBrokerFactory = $messageBrokerFactory;
    }

    /**
     * Handle the event.
     */
    public function handle(TrainingCompleted $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'training_completed',
            'data' => [
                'training_id' => $event->training->id,
                'completion_type' => $event->training->completion_type,
                'started_at' => $event->training->started_at,
                'completed_at' => $event->training->completed_at,
            ]
        ];

        $messageBroker->publish('api.training', json_encode($payload));
    }
}

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
        $training = $event->training;

        $bus = $this->messageBrokerFactory->create();

        $bus->publish('training', serialize($training));
    }
}

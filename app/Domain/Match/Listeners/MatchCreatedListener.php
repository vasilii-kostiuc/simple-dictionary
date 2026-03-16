<?php

namespace App\Domain\Training\Listeners;

use App\Domain\Match\Events\MatchCreatedEvent;
use App\Domain\Training\Events\TrainingCompleted;
use App\Domain\Training\Events\TrainingStartedEvent;
use App\Domain\Training\Factories\CompletionConditionFactory;
use App\Http\Resources\Match\MatchResource;
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
    public function handle(MatchCreatedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_created',
            'data' => [
                MatchResource::make($event->match)->toArray(null)
            ]
        ];

        $messageBroker->publish('api.match', json_encode($payload));
    }
}

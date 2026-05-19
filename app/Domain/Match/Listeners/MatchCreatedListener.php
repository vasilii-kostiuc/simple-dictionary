<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchCreatedEvent;
use App\Http\Resources\Match\MatchResource;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class MatchCreatedListener
{
    public function __construct(
        private BrokerFactory $messageBrokerFactory
    ) {}

    /**
     * Handle the event.
     */
    public function handle(MatchCreatedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_created',
            'data' => MatchResource::make($event->match),
        ];

        $messageBroker->publish('api.match', json_encode($payload));
    }
}

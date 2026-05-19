<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchStartedEvent;
use App\Http\Resources\Match\MatchResource;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class MatchStartedListener
{
    public function __construct(
        private BrokerFactory $messageBrokerFactory
    ) {}

    /**
     * Handle the event.
     */
    public function handle(MatchStartedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_started',
            'data' => MatchResource::make($event->match),
        ];

        $messageBroker->publish('api.match', json_encode($payload));
    }
}

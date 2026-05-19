<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchNextStepGeneratedEvent;
use App\Http\Resources\Match\MatchStepResource;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class MatchNextStepGeneratedListener
{
    public function __construct(
        private BrokerFactory $messageBrokerFactory
    ) {}

    public function handle(MatchNextStepGeneratedEvent $event): void
    {
        $broker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'next_step_generated',
            'data' => MatchStepResource::make($event->nextStep),
        ];

        $broker->publish('api.match', json_encode($payload));
    }
}

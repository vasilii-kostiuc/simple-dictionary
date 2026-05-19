<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Services\MatchSummaryBuilder;
use App\Http\Resources\Match\MatchSummaryResource;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class PublishMatchSummaryOnUserAnsweredListener
{
    public function __construct(
        private BrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {}

    public function handle(MatchUserAnsweredEvent $event): void
    {
        $broker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_summary',
            'data' => MatchSummaryResource::make($this->matchSummaryBuilder->build($event->match)),
        ];

        $broker->publish('api.match', json_encode($payload));
    }
}

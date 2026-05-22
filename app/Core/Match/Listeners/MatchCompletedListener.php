<?php

namespace App\Core\Match\Listeners;

use App\Core\Match\Events\MatchCompletedEvent;
use App\Core\Match\Factories\CompletionConditionFactory;
use App\Core\Match\Services\MatchSummaryBuilder;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class MatchCompletedListener
{
    public function __construct(
        private CompletionConditionFactory $completionConditionFactory,
        private BrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {}

    /**
     * Handle the event.
     */
    public function handle(MatchCompletedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_completed',
            'data' => $this->matchSummaryBuilder->build($event->match),
        ];

        $messageBroker->publish('api.match', json_encode($payload));
    }
}

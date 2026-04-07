<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchCompletedEvent;
use App\Domain\Match\Factories\CompletionConditionFactory;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;
use App\Domain\Match\Services\MatchSummaryBuilder;

class MatchCompletedListener
{
    public function __construct(
        private CompletionConditionFactory $completionConditionFactory,
        private MessageBrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(MatchCompletedEvent $event): void
    {
        info(__METHOD__);

        $messageBroker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_completed',
            'data' => $this->matchSummaryBuilder->build($event->match)
        ];

        $messageBroker->publish('api.match', json_encode($payload));
    }
}

<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchUserStatusChangedEvent;
use App\Domain\Match\Services\MatchSummaryBuilder;
use App\Http\Resources\Match\MatchSummaryResource;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

class PublishMatchSummaryOnUserStatusChangedListener
{
    public function __construct(
        private MessageBrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder,
    ) {}

    public function handle(MatchUserStatusChangedEvent $event): void
    {
        $broker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_summary',
            'data' => MatchSummaryResource::make($this->matchSummaryBuilder->build($event->match)),
        ];

        $broker->publish('api.match', json_encode($payload));
    }
}

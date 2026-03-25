<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Service\MatchSummaryBuilder;
use App\Http\Resources\Match\MatchSummaryResource;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

class PublishMatchSummaryOnStepSkippedListener
{
    public function __construct(
        private MessageBrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {}

    public function handle(MatchStepSkippedEvent $event): void
    {
        $broker = $this->messageBrokerFactory->create();

        $payload = [
            'type' => 'match_summary',
            'data' => MatchSummaryResource::make($this->matchSummaryBuilder->build($event->match)),
        ];

        $broker->publish('api.match', json_encode($payload));
    }
}

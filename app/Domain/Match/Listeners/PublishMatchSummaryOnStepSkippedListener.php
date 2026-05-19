<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Services\MatchSummaryBuilder;
use App\Http\Resources\Match\MatchSummaryResource;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class PublishMatchSummaryOnStepSkippedListener
{
    public function __construct(
        private BrokerFactory $messageBrokerFactory,
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {}

    public function handle(MatchStepSkippedEvent $event): void
    {
        $broker = $this->messageBrokerFactory->create();

        info('Publishing match summary due to step skipped', ['match_id' => $event->match->id, 'step_id' => $event->step->id]);
        $payload = [
            'type' => 'match_summary',
            'data' => MatchSummaryResource::make($this->matchSummaryBuilder->build($event->match)),
        ];

        info('Match summary payload prepared', ['payload' => $payload]);
        $broker->publish('api.match', json_encode($payload));
    }
}

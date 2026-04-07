<?php

namespace App\Domain\Match\Listeners;

use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Factories\CompletionConditionFactory;
use App\Domain\Match\Services\MatchService;

class CheckMatchCompletionListener
{
    public function __construct(
        private readonly CompletionConditionFactory $completionConditionFactory,
        private readonly MatchService $matchService,
    ) {
    }

    public function handle(MatchUserAnsweredEvent|MatchStepSkippedEvent $event): void
    {
        $match = $event->match->refresh();

        $completionCondition = $this->completionConditionFactory->create($match);

        if ($completionCondition->isCompleted()) {
            $this->matchService->complete($match);
        }
    }
}

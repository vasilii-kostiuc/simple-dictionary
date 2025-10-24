<?php

namespace App\Domain\Training\Listeners;

use App\Domain\Training\Events\StepAttemptEvent;
use App\Domain\Training\Events\StepSkippedEvent;
use App\Domain\Training\Factories\CompletionConditionFactory;

class TrainingStepSkipAttemptListener
{
    private CompletionConditionFactory $completionConditionFactory;

    /**
     * Create the event listener.
     */
    public function __construct(CompletionConditionFactory $completionConditionFactory)
    {
        $this->completionConditionFactory = $completionConditionFactory;
    }

    /**
     * Handle the event.
     */
    public function handle(StepAttemptEvent|StepSkippedEvent $event): void
    {
        info(__METHOD__);
        $completionCondition = $this->completionConditionFactory->create($event->training);

        if ($completionCondition->isCompleted()) {
            info('Training completed');
            $event->training->completeTraining();
        }
    }
}

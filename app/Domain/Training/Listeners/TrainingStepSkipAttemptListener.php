<?php

namespace App\Domain\Training\Listeners;

use App\Domain\Training\Enums\TrainingCompletionReason;
use App\Domain\Training\Events\StepAttemptEvent;
use App\Domain\Training\Events\StepSkippedEvent;
use App\Domain\Training\Factories\CompletionConditionFactory;
use App\Domain\Training\Service\TrainingService;

class TrainingStepSkipAttemptListener
{
    private CompletionConditionFactory $completionConditionFactory;
    private TrainingService $trainingService;
    /**
     * Create the event listener.
     */
    public function __construct(CompletionConditionFactory $completionConditionFactory, TrainingService $trainingService)
    {
        $this->completionConditionFactory = $completionConditionFactory;
        $this->trainingService = $trainingService;
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

            $this->trainingService->complete($event->training);
        }
    }
}

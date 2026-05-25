<?php

namespace App\Core\Training\Listeners;

use App\Core\Training\Events\StepAttemptEvent;
use App\Core\Training\Events\StepSkippedEvent;
use App\Core\Training\Factories\CompletionConditionFactory;
use App\Core\Training\Services\TrainingService;

class CheckTrainingCompletionListener
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

<?php

namespace App\Core\Training\Services;

use App\Core\Step\Enums\StepType;
use App\Core\Step\StepAttemptVerifierFactory;
use App\Core\Training\Events\StepAttemptEvent;
use App\Core\Training\Models\TrainingStep;
use App\Core\Training\Models\TrainingStepAttempt;

class TrainingStepAttemptService
{
    public function __construct(
        private readonly StepAttemptVerifierFactory $stepAttemptVerifierFactory
    ) {}

    public function create(TrainingStep $trainingStep, array $attemptData): TrainingStepAttempt
    {
        $stepVerifier = $this->stepAttemptVerifierFactory->create(StepType::from($trainingStep->step_type_id));

        $isCorrect = $stepVerifier->verify($trainingStep->step_data, $attemptData);

        $subIndex = $trainingStep->getNextAttemptSubIndex();

        $attempt = TrainingStepAttempt::create([
            'training_step_id' => $trainingStep->id,
            'attempt_data' => $attemptData,
            'sub_index' => $subIndex,
            'is_correct' => $isCorrect,
        ]);

        event(new StepAttemptEvent($trainingStep->training, $attempt));

        return $attempt;
    }
}

<?php

namespace App\Domain\Training\Service;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Events\StepAttemptEvent;
use App\Domain\Training\Factories\StepAttemptVerifierFactory;
use App\Domain\Training\Models\TrainingStep;
use App\Domain\Training\Models\TrainingStepAttempt;

class TrainingStepAttemptService
{
    private StepAttemptVerifierFactory $stepAttemptVerifierFactory;

    public function __construct(StepAttemptVerifierFactory $stepAttemptVerifierFactory)
    {
        $this->stepAttemptVerifierFactory = $stepAttemptVerifierFactory;
    }

    public function create(TrainingStep $trainingStep, array $attemptData): TrainingStepAttempt
    {
        $stepVerifier = $this->stepAttemptVerifierFactory->create(TrainingStepType::from($trainingStep->step_type_id));

        $isCorrect = $stepVerifier->verify($trainingStep, $attemptData);

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

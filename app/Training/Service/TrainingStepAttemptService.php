<?php

namespace App\Training\Service;

use App\Training\Enums\TrainingStepType;
use App\Training\Factories\StepAttemptVerifierFactory;
use App\Training\Models\TrainingStep;
use App\Training\Models\TrainingStepAttempt;

class TrainingStepAttemptService
{
    private StepAttemptVerifierFactory $stepAttemptVerifierFactory;

    public function __construct(StepAttemptVerifierFactory $stepAttemptVerifierFactory)
    {
        $this->stepAttemptVerifierFactory = $stepAttemptVerifierFactory;
    }

    public function create(TrainingStep $trainingStep, array $attemptData): TrainingStepAttempt
    {
        $stepVerifier = $this->stepAttemptVerifierFactory->create(TrainingStepType::from($trainingStep->step_type));

        $isCorrect = $stepVerifier->verify($trainingStep, $attemptData);

        $subIndex = $trainingStep->getNextAttemptSubIndex();

        return TrainingStepAttempt::create([
            'training_step_id' => $trainingStep->id,
            'attempt_data' => $attemptData,
            'sub_index' => $subIndex,
            'is_correct' => $isCorrect,
        ]);
    }
}

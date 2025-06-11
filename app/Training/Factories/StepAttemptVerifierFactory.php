<?php

namespace App\Training\Factories;

use App\Training\Enums\TrainingStepType;
use App\Training\Models\TrainingStep;
use App\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepAttemptVerifier;
use App\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepVerifier;
use App\Training\StepAttemptVerifiers\EstablishComplianceStepAttemptVerifier;
use App\Training\StepAttemptVerifiers\StepAttemptVerifier;
use App\Training\StepAttemptVerifiers\StepVerifier;
use App\Training\StepAttemptVerifiers\WriteCorrectAnswerStepAttemptVerifier;
use App\Training\StepAttemptVerifiers\WriteCorrectAnswerStepVerifier;

class StepAttemptVerifierFactory
{
    public function create(TrainingStepType $stepType): StepAttemptVerifier
    {
        return match ($stepType) {
            TrainingStepType::ChooseCorrectAnswer => new ChooseCorrectAnswerStepAttemptVerifier(),
            TrainingStepType::WriteCorrectAnswer => new WriteCorrectAnswerStepAttemptVerifier(),
            TrainingStepType::EstablishCompliance => new EstablishComplianceStepAttemptVerifier(),
            default => throw new \Exception('Step Verifier not found'),
        };
    }
}

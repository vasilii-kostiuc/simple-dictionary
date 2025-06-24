<?php

namespace App\Domain\Training\Factories;

use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\EstablishComplianceStepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\StepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\WriteCorrectAnswerStepAttemptVerifier;
use App\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepVerifier;
use App\Training\StepAttemptVerifiers\StepVerifier;
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

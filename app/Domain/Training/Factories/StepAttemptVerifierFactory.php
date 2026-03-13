<?php

namespace App\Domain\Training\Factories;

use App\Domain\Step\Enums\StepType;
use App\Domain\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\EstablishComplianceStepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\StepAttemptVerifier;
use App\Domain\Training\StepAttemptVerifiers\WriteCorrectAnswerStepAttemptVerifier;
use App\Training\StepAttemptVerifiers\ChooseCorrectAnswerStepVerifier;
use App\Training\StepAttemptVerifiers\StepVerifier;
use App\Training\StepAttemptVerifiers\WriteCorrectAnswerStepVerifier;

class StepAttemptVerifierFactory
{
    public function create(StepType $stepType): StepAttemptVerifier
    {
        return match ($stepType) {
            StepType::ChooseCorrectAnswer => new ChooseCorrectAnswerStepAttemptVerifier(),
            StepType::WriteCorrectAnswer => new WriteCorrectAnswerStepAttemptVerifier(),
            StepType::EstablishCompliance => new EstablishComplianceStepAttemptVerifier(),
            default => throw new \Exception('Step Verifier not found'),
        };
    }
}

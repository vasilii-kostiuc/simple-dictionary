<?php

namespace App\Domain\Step;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepAttemptVerifiers\ChooseCorrectAnswerStepAttemptVerifier;
use App\Domain\Step\StepAttemptVerifiers\EstablishComplianceStepAttemptVerifier;
use App\Domain\Step\StepAttemptVerifiers\StepAttemptVerifier;
use App\Domain\Step\StepAttemptVerifiers\WriteCorrectAnswerStepAttemptVerifier;

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

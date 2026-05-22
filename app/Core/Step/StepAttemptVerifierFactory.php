<?php

namespace App\Core\Step;

use App\Core\Step\Enums\StepType;
use App\Core\Step\StepAttemptVerifiers\ChooseCorrectAnswerStepAttemptVerifier;
use App\Core\Step\StepAttemptVerifiers\EstablishComplianceStepAttemptVerifier;
use App\Core\Step\StepAttemptVerifiers\StepAttemptVerifier;
use App\Core\Step\StepAttemptVerifiers\WriteCorrectAnswerStepAttemptVerifier;

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

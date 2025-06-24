<?php

namespace App\Domain\Training\StepAttemptVerifiers;

use App\Domain\Training\Models\TrainingStep;

interface StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool;
}

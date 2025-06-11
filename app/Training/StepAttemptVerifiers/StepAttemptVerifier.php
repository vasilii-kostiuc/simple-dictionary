<?php

namespace App\Training\StepAttemptVerifiers;

use App\Training\Models\TrainingStep;

interface StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool;
}

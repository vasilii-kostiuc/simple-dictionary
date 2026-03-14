<?php

namespace App\Domain\Step\StepAttemptVerifiers;

interface StepAttemptVerifier
{
    public function verify(array $step_data, array $attemptData): bool;
}

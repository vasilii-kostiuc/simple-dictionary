<?php

namespace App\Core\Step\StepAttemptVerifiers;

interface StepAttemptVerifier
{
    public function verify(array $step_data, array $attemptData): bool;
}

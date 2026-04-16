<?php

namespace App\Domain\Step\StepAttemptVerifiers;

class EstablishComplianceStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(array $step_data, array $attemptData): bool
    {

        $word_id = $attemptData['word_id'];
        $answer_id = $attemptData['answer_id'];

        return $word_id === $answer_id;
    }
}

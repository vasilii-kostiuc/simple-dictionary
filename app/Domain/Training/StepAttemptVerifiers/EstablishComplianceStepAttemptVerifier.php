<?php

namespace App\Domain\Training\StepAttemptVerifiers;

use App\Domain\Training\Models\TrainingStep;

class EstablishComplianceStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool
    {

        $word_id = $attemptData['word_id'];
        $answer_id = $attemptData['answer_id'];

        return $word_id === $answer_id;
    }
}

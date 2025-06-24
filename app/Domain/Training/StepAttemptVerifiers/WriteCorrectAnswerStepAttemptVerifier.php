<?php

namespace App\Domain\Training\StepAttemptVerifiers;

use App\Domain\Training\Models\TrainingStep;

class WriteCorrectAnswerStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool
    {
        $attemptAnswer= trim($attemptData['word']);

        $acceptableAnswers = $trainingStep->step_data['acceptable_answers'];

        $isCorrect = in_array($attemptAnswer, $acceptableAnswers);

        return $isCorrect;
    }
}

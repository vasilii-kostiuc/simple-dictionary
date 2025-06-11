<?php

namespace App\Training\StepAttemptVerifiers;

use App\Training\Models\TrainingStep;

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

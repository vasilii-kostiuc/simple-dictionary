<?php

namespace App\Domain\Step\StepAttemptVerifiers;

class WriteCorrectAnswerStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(array $step_data, array $attemptData): bool
    {
        $attemptAnswer = trim($attemptData['word']);

        $acceptableAnswers = $step_data['acceptable_answers'];

        $isCorrect = in_array($attemptAnswer, $acceptableAnswers);

        return $isCorrect;
    }
}

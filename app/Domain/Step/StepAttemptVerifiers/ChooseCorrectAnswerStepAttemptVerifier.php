<?php

namespace App\Domain\Step\StepAttemptVerifiers;

class ChooseCorrectAnswerStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(array $step_data, array $attemptData): bool
    {
        $atteptWordId = $attemptData['word_id'];

        $stepWordId = $step_data['word_id'];

        $words = $step_data['answers'];

        $wordIds = array_column($words, 'word_id');

        if (!in_array($atteptWordId, $wordIds)) {
            return false;
        }

        return $atteptWordId === $stepWordId;;
    }
}

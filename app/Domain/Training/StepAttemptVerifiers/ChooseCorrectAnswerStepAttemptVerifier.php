<?php

namespace App\Domain\Training\StepAttemptVerifiers;

use App\Domain\Training\Models\TrainingStep;

class ChooseCorrectAnswerStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool
    {
        $atteptWordId = $attemptData['word_id'];

        $stepWordId = $trainingStep->step_data['word_id'];

        $words = $trainingStep->step_data['answers'];

        $wordIds = array_column($words, 'word_id');

        if(!in_array($atteptWordId, $wordIds)) {
            return false;
        }

        return $atteptWordId === $stepWordId;;
    }
}

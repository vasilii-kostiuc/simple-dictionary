<?php

namespace App\Training\StepAttemptVerifiers;

use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\TrainingStep;
use App\Training\StepAttemptVerifiers\StepAttemptVerifier;
use App\Training\Steps\WordTrainingStep;

class ChooseCorrectAnswerStepAttemptVerifier implements StepAttemptVerifier
{
    public function verify(TrainingStep $trainingStep, array $attemptData): bool
    {
        $atteptWordId = $attemptData['word_id'];

        $stepWordId = $trainingStep->step_data['word_id'];

        $words = $trainingStep->step_data['words'];

        $wordIds = array_column($words, 'id');

        if(!in_array($atteptWordId, $wordIds)) {
            return false;
        }

        return $atteptWordId === $stepWordId;;
    }
}

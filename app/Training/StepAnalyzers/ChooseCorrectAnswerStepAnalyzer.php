<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use App\Training\StepAnalyzers\StepAnalyzer;
use App\Training\Steps\WordTrainingStep;

class ChooseCorrectAnswerStepAnalyzer implements StepAnalyzer
{
    public function isPassed(WordTrainingStep $trainingStep, array $attemptData): bool
    {
        return true;
    }
}

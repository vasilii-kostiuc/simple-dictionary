<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use App\Training\StepAnalyzers\StepAnalyzer;

class ChooseCorrectAnswerStepAnalyzer implements StepAnalyzer
{
    public function isPassed(TrainingStep $trainingStep, array $attemptData): bool
    {
        return true;
    }
}

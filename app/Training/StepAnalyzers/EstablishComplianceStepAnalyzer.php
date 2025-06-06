<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use App\Training\Steps\WordTrainingStep;

class EstablishComplianceStepAnalyzer implements StepAnalyzer
{
    public function isPassed(WordTrainingStep $trainingStep, array $attemptData): bool
    {
        // TODO: Implement siPassed() method.
    }
}

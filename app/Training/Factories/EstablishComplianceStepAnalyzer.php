<?php

namespace App\Training\Factories;

use App\Training\Models\TrainingStep;
use App\Training\StepAnalyzers\StepAnalyzer;

class EstablishComplianceStepAnalyzer implements StepAnalyzer
{
    public function isPassed(TrainingStep $trainingStep, array $attemptData): bool
    {
        // TODO: Implement isPassed() method.
    }
}

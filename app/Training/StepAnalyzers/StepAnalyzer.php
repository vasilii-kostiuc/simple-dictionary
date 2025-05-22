<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;

interface StepAnalyzer
{
    public function isPassed(TrainingStep $trainingStep, array $attemptData): bool;
}

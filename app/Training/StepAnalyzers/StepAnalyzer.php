<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use App\Training\Steps\WordTrainingStep;

interface StepAnalyzer
{
    public function isPassed(WordTrainingStep $trainingStep, array $attemptData): bool;
}

<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use Nette\NotImplementedException;

class WriteCorrectAnswerStepAnalyzer implements StepAnalyzer
{

    public function isPassed(TrainingStep $trainingStep, array $attemptData): bool
    {
        throw new NotImplementedException();
    }
}

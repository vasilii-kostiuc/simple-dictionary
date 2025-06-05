<?php

namespace App\Training\StepAnalyzers;

use App\Training\Models\TrainingStep;
use App\Training\Steps\WordTrainingStep;
use Nette\NotImplementedException;

class WriteCorrectAnswerStepAnalyzer implements StepAnalyzer
{
    public function isPassed(WordTrainingStep $trainingStep, array $attemptData): bool
    {
        throw new NotImplementedException();
    }
}

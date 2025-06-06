<?php

namespace App\Training\Service;

use App\Training\Factories\StepAnalyzerFactory;
use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\TrainingStep;

class StepCheckService
{
    public function check(TrainingStep $trainingStep, array $attemptData): bool
    {
        $stepAnalyzer = $this->stepAnalyzerFactory->create($trainingStep);

        return $stepAnalyzer->isPassed($trainingStep, $attemptData);
    }
}

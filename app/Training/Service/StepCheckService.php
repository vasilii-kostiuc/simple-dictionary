<?php

namespace App\Training\Service;

use App\Training\Factories\StepAnalyzerFactory;
use App\Training\Models\TrainingStep;

class StepCheckService
{
    private StepAnalyzerFactory $stepAnalyzerFactory;

    public function __construct(StepAnalyzerFactory $stepAnalyzerFactory)
    {
        $this->stepAnalyzerFactory = $stepAnalyzerFactory;
    }

    public function check(TrainingStep $trainingStep, array $attemptData): bool
    {
        $stepAnalyzer = $this->stepAnalyzerFactory->create($trainingStep);
        return $stepAnalyzer->isPassed($trainingStep, $attemptData);
    }
}

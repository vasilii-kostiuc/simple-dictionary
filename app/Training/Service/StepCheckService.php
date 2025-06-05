<?php

namespace App\Training\Service;

use App\Training\Factories\StepAnalyzerFactory;
use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\TrainingStep;

class StepCheckService
{
    private StepAnalyzerFactory $stepAnalyzerFactory;
    private TrainingStepFactory $trainingStepFactory;

    public function __construct(StepAnalyzerFactory $stepAnalyzerFactory,TrainingStepFactory $trainingStepFactory)
    {
        $this->stepAnalyzerFactory = $stepAnalyzerFactory;
        $this->trainingStepFactory = $trainingStepFactory;
    }

    public function check(TrainingStep $trainingStep, array $attemptData): bool
    {
        $stepAnalyzer = $this->stepAnalyzerFactory->create($trainingStep);
        $wordTrainingStep = $this->trainingStepFactory->createStepFromData($trainingStep->step_data, $attemptData);

        return $stepAnalyzer->isPassed($wordTrainingStep, $attemptData);
    }
}

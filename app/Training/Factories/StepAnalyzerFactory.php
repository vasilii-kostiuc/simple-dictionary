<?php

namespace App\Training\Factories;

use App\Training\Enums\TrainingStepType;
use App\Training\Models\TrainingStep;
use App\Training\StepAnalyzers\ChooseCorrectAnswerStepAnalyzer;
use App\Training\StepAnalyzers\EstablishComplianceStepAnalyzer;
use App\Training\StepAnalyzers\StepAnalyzer;
use App\Training\StepAnalyzers\WriteCorrectAnswerStepAnalyzer;

class StepAnalyzerFactory
{
    public function create(TrainingStep $trainingStep): StepAnalyzer
    {
        return match ($trainingStep->training_type) {
            TrainingStepType::ChooseCorrectAnswer => new ChooseCorrectAnswerStepAnalyzer(),
            TrainingStepType::WriteCorrectAnswer => new WriteCorrectAnswerStepAnalyzer(),
            TrainingStepType::EstablishCompliance => new EstablishComplianceStepAnalyzer(),
            default => throw new \Exception('Step analyzer not found'),
        };
    }
}

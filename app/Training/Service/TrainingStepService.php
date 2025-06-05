<?php

namespace App\Training\Service;

use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Steps\WordTrainingStep;

class TrainingStepService
{
    public function create(WordTrainingStep $trainingStep, Training $training): WordTrainingStep
    {
        TrainingStep::create([
            'training_id' => $training->id,
            'step_data' => $trainingStep->toArray(),
            'training_step_type' => $trainingStep->getTrainingStepType()->value,
            'step_number' => $this->calculateNextStepNumber($training),
        ]);

        return $trainingStep;
    }

    private function calculateNextStepNumber(Training $training): int
    {
        return $training->steps()->count() + 1;
    }
}

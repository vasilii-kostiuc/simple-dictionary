<?php

namespace App\Training\Service;

use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Steps\WordTrainingStep;

class TrainingStepService
{
    public function create(WordTrainingStep $wordTrainingStep, Training $training): TrainingStep
    {
         $step = TrainingStep::create([
            'training_id' => $training->id,
            'step_data' => $wordTrainingStep->toArray(),
            'step_type_id' => $wordTrainingStep->getTrainingStepType()->value,
            'step_number' => $this->calculateNextStepNumber($training),
        ]);

        return $step;
    }

    private function calculateNextStepNumber(Training $training): int
    {
        return $training->steps()->count() + 1;
    }
}

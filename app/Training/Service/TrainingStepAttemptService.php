<?php

namespace App\Training\Service;

use App\Training\Models\TrainingStep;
use App\Training\Models\TrainingStepAttempt;

class TrainingStepAttemptService
{
    public function create(TrainingStep $trainingStep, array $atemptData, bool $isPassed): TrainingStepAttempt
    {
        return TrainingStepAttempt::create([
            'training_step_id' => $trainingStep->id,
            'attempt_data' => $atemptData,
            'is_correct' => $isPassed,
        ]);
    }
}

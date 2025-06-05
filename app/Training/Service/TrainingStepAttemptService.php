<?php

namespace App\Training\Service;

use App\Training\Models\TrainingStepAttempt;

class TrainingStepAttemptService
{
    public function create(int $trainingSterpId, array $atemptData, bool $isPassed): TrainingStepAttempt
    {
        return TrainingStepAttempt::create([
            'training_step_id' => $trainingSterpId,
            'attempt_data' => $atemptData,
            'is_passed' => $isPassed,
        ]);
    }
}

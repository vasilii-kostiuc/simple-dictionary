<?php

namespace App\Training\Service;

use App\Training\Models\TrainingStep;

class TrainingStepProgressService
{

    public function getProgress(TrainingStep $step): array
    {
        $total = $step->required_answers_count;

        $lastAttemptNum = $step->attempts()->max('attempt_number');
        if (!$lastAttemptNum) {
            return [
                'total' => $total,
                'answered' => 0,
                'completed' => false,
            ];
        }

        $answered = $step->attempts()
            ->where('attempt_number', $lastAttemptNum)
            ->where('is_correct', true)
            ->count();

        return [
            'total' => $total,
            'answered' => $answered,
            'completed' => $answered >= $total,
        ];
    }

}

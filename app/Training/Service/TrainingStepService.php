<?php

namespace App\Training\Service;

use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Models\TrainingStepAttempt;
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

    public function isStepPassed($step): bool
    {
        $lastAttemptNum = TrainingStepAttempt::where([
            'step_id' => $step->id,
        ])->max('attempt_number');

        if (!$lastAttemptNum) {
            return false;
        }

        $attempts = $step->attempts()->where([
            'attempt_number' => $lastAttemptNum,
        ])->get();

        if ($attempts->isEmpty()) {
            return false;
        }

        $correctAnswers = $attempts->where('is_correct', true)->count();

        return $correctAnswers >= $step->required_answers_count;
    }

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


    private function calculateNextStepNumber(Training $training): int
    {
        return $training->steps()->count() + 1;
    }
}

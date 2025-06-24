<?php

namespace App\Domain\Training\Service;

use App\Domain\Training\Models\TrainingStep;

class TrainingStepProgressService
{
    public function getProgress(TrainingStep $step): object
    {
        $total = $step->required_answers_count;

        $answered = $this->getCorrectAnswersCount($step);

        return (object)[
            'id' => $step->id,
            'required_answers_count' => $total,
            'answered' => $answered,
            'is_passed' => $step->isPassed(),
            'skipped' => $step->skipped,
            'skipped_at' => $step->skipped,
        ];
    }

    private function getCorrectAnswersCount(TrainingStep $step): int
    {
        $lastAttemptNum = $step->attempts()->max('attempt_number');

        if (!$lastAttemptNum) {
            return 0;
        }

        return $step->attempts()
            ->where('attempt_number', $lastAttemptNum)
            ->where('is_correct', true)
            ->count();
    }


}

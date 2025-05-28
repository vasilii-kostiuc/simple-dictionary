<?php

namespace App\Training\Service;

use App\Training\Models\Training;

class TrainingService
{
    public function create(array $data): Training
    {
        $training = new Training;
        $training->fill($data);
        $training->save();

        return $training;
    }

    public function isLastStepCompletedOrSkipped(Training $training): bool
    {
        $lastStep = $training->steps()->orderBy('id', 'desc')->first();

        if ($lastStep === null) {
            return true;
        }

        if ($lastStep->isPassed() || $lastStep->is_skipped) {
            return true;
        }

        return false;
    }
}

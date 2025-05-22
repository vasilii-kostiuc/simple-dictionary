<?php

namespace App\Training\Service;

use App\Training\Models\Training;
use App\Training\Models\TrainingStep;

class TrainingService
{
    public function create(array $data): Training
    {
        $training = new Training;
        $training->fill($data);
        $training->save();

        return $training;
    }

    public function completeStep(TrainingStep $trainingStep): bool
    {

    }
}

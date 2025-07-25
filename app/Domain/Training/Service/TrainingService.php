<?php

namespace App\Domain\Training\Service;

use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Models\Training;

class TrainingService
{
    public function create(array $data): Training
    {
        $training = new Training;
        $training->fill($data);
        $training->status = TrainingStatus::New;
        $training->save();

        return $training;
    }

    public function start(Training $training)
    {
        $training->status = TrainingStatus::InProgress;
        $training->started_at = now();
        $training->save();

        return $training;
    }

}

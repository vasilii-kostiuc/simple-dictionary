<?php

namespace App\Domain\Training\Service;

use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Events\TrainingStartedEvent;
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
        if($training->status == TrainingStatus::InProgress) {
            return $training;
        }

        $training->status = TrainingStatus::InProgress;
        $training->started_at = now();
        $training->save();


        event(new TrainingStartedEvent($training));
        return $training;
    }

}

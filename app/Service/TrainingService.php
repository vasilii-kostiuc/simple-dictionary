<?php

namespace App\Service;

use App\Models\Training\Training;

class TrainingService
{
    public function create(array $data): Training
    {
        $training = new Training;
        $training->fill($data);
        $training->save();

        return $training;
    }
}

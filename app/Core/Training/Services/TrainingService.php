<?php

namespace App\Core\Training\Services;

use App\Core\Training\Enums\TrainingCompletionReason;
use App\Core\Training\Enums\TrainingStatus;
use App\Core\Training\Events\TrainingStartedEvent;
use App\Core\Training\Models\Training;

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

    public function start(Training $training): Training
    {
        if ($training->status == TrainingStatus::InProgress) {
            return $training;
        }

        $training->status = TrainingStatus::InProgress;
        $training->started_at = now();
        $training->save();

        event(new TrainingStartedEvent($training));

        return $training;
    }

    public function complete(Training $training, ?TrainingCompletionReason $reason = null, ?array $details = []): Training
    {
        if ($reason === null) {
            $reason = TrainingCompletionReason::defaultForCompletionType($training->completion_type);
        }

        $training->completeTraining($reason, $details);

        return $training;
    }
}

<?php

namespace App\Domain\Training\Models;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Events\TrainingCompleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    protected $fillable = ['dictionary_id', 'training_type_id', 'completion_type', 'completion_params', 'status', 'started_at', 'completed_at'];

    public function dictionary(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'dictionary_id', 'id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(TrainingStep::class);
    }

    public function lastStep(): ?TrainingStep
    {
        return $this->steps()->orderBy('step_number', 'desc')->first();
    }

    public function completeTraining(): void
    {
        $this->updateCompletionStatus();
        $this->save();

        TrainingCompleted::dispatch($this);
    }

    private function updateCompletionStatus(): void
    {
        $this->status = TrainingStatus::Completed;
        $this->completed_at = now();
    }

}

<?php

namespace App\Domain\Training\Models;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Training\Enums\TrainingCompletionReason;
use App\Domain\Training\Enums\TrainingCompletionType;
use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Events\TrainingCompleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    protected $fillable = ['dictionary_id', 'training_type_id', 'completion_type', 'completion_type_params', 'status', 'started_at', 'completed_at', 'completion_reason', 'completion_details'];

    protected $casts = ['completion_type_params' => 'array', 'completion_details' => 'array', 'status' => TrainingStatus::class, 'completion_type' => TrainingCompletionType::class, 'completion_reason' => TrainingCompletionReason::class, 'started_at' => 'datetime', 'completed_at' => 'datetime'];

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

    public function completeTraining(TrainingCompletionReason $reason, ?array $details = null): void
    {
        $this->validateCompletionReason($reason);
        $this->updateCompletionStatus($reason, $details);
        $this->save();

        TrainingCompleted::dispatch($this);
    }

    private function validateCompletionReason(TrainingCompletionReason $reason): void
    {
        $allowedReasons = TrainingCompletionReason::forCompletionType($this->completion_type);

        if (!in_array($reason, $allowedReasons, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Completion reason "%s" is not allowed for training type "%s"',
                    $reason->name,
                    $this->completion_type->value
                )
            );
        }
    }

    private function updateCompletionStatus(TrainingCompletionReason $reason, ?array $details): void
    {
        $this->status = TrainingStatus::Completed;
        $this->completed_at = now();
        $this->completion_reason = $reason;
        $this->completion_details = $details;
    }

}

<?php

namespace App\Training\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingStep extends Model
{
    protected $fillable = ['training_id', 'step_type_id', 'step_number'];

    public function getTraining(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TrainingStepAttempt::class);
    }

    public function isPassed(): bool
    {
        return $this->attempts()
            ->where('is_passed', true)
            ->exists();
    }
    public function isPassedOrSkipped(): bool
    {
        return $this->isPassed() || $this->is_skipped;
    }


    public function getNextAttemptSubIndex(): int
    {
        if ($this->attempts->isEmpty()) {
            return 1;
        }

        return $this->attempts()->max('sub_index') + 1;
    }

}

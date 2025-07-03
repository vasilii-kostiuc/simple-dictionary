<?php

namespace App\Domain\Training\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingStep extends Model
{
    protected $fillable = [
        'training_id',
        'step_type_id',
        'step_number',
        'skipped',
        'required_answers_count',
        'step_data'
    ];

    protected $casts = [
        'step_data' => 'array'
    ];


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
        $lastAttemptNum = $this->attempts()->max('attempt_number');

        if (!$lastAttemptNum) {
            return false;
        }

        $attempts = $this->attempts()->where([
            'attempt_number' => $lastAttemptNum,
        ])->get();

        if ($attempts->isEmpty()) {
            return false;
        }

        $correctAnswers = $attempts->where('is_correct', true)->count();

        return $correctAnswers >= $this->required_answers_count;
    }

    public function isPassedOrSkipped(): bool
    {
        return $this->isPassed() || $this->skipped;
    }


    public function getNextAttemptSubIndex(): int
    {
        if ($this->attempts->isEmpty()) {
            return 1;
        }

        return $this->attempts()->max('sub_index') + 1;
    }

}

<?php

namespace App\Domain\Match\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class MatchStep extends Model
{
    protected $fillable = [
        'match_id',
        'user_id',
        'guest_id',
        'step_type_id',
        'step_number',
        'step_data',
        'required_answers_count',
        'skipped',
        'skipped_at'
    ];

    protected $casts = [
        'step_data' => 'array',
        'skipped' => 'boolean',
        'skipped_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(MatchStepAttempt::class);
    }

    public function getParticipantIdentifier(): string
    {
        return (string) ($this->user_id ?? $this->guest_id);
    }

    public function isForGuest(): bool
    {
        return $this->user_id === null;
    }

    public function isPassed(): bool
    {
        $lastAttemptNum = $this->attempts()->max('attempt_number');

        if (! $lastAttemptNum) {
            return false;
        }

        $correctAnswers = $this->attempts()
            ->where('attempt_number', $lastAttemptNum)
            ->where('is_correct', true)
            ->count();

        return $correctAnswers >= $this->required_answers_count;
    }

    public function correctAnswers()
    {
        return $this->attempts()->where('is_correct', true)->get();
    }

    public function hasAttempts(): bool
    {
        return $this->attempts()->exists();
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

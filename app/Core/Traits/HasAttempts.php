<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAttempts
{
    public function hasAttempts(): bool
    {
        return $this->attempts()->exists();
    }

    public function isPassed(): bool
    {
        $lastAttemptNum = $this->attempts()->max('attempt_number');

        if (! $lastAttemptNum) {
            return false;
        }

        $attempts = $this->attempts()->where([
            'attempt_number' => $lastAttemptNum,
        ])->get();

        if ($attempts->isEmpty()) {
            return false;
        }

        $correctAnswers = $this->correctAnswers()->count();

        return $correctAnswers >= $this->required_answers_count;
    }

    public function correctAnswers()
    {
        return $this->attempts()->where('is_correct', true)->get();
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

    abstract public function attempts(): HasMany;
}

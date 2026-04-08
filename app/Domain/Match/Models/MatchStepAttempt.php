<?php

namespace App\Domain\Match\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchStepAttempt extends Model
{
    protected $fillable = [
        'match_step_id',
        'attempt_number',
        'sub_index',
        'attempt_data',
        'is_correct',
    ];

    protected $casts = [
        'attempt_data' => 'array',
        'is_correct' => 'boolean',
    ];

    public function matchStep(): BelongsTo
    {
        return $this->belongsTo(MatchStep::class);
    }
}

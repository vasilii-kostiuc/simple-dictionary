<?php

namespace App\Domain\Training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingStepAttempt extends Model
{
    protected $fillable = [
        'training_step_id',
        'attempt_data',
        'sub_index',
        'is_correct',
    ];

    protected $casts = [
        'attempt_data' => 'array',
        'is_passed' => 'boolean',
    ];
}

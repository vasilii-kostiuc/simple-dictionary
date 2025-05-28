<?php

namespace App\Training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingStepAttempt extends Model
{
    protected $fillable = [
        'training_id',
        'attempt_data',
        'is_passed',
    ];

    protected $casts = [
        'attempt_data' => 'array',
        'is_passed' => 'boolean',
    ];
}

<?php

namespace App\Training\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingStep extends Model
{
    protected $fillable = ['training_id', 'step_type_id', 'step_number'];

    public function getTraining(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}

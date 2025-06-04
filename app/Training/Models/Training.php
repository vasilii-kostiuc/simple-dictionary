<?php

namespace App\Training\Models;

use App\Models\Dictionary;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
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
}

<?php

namespace App\Training\Models;

use App\Models\Dictionary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    protected $fillable = ['dictionary_id', 'training_type_id'];

    public function getDictionary():belongsTo
    {
        return $this->belongsTo(Dictionary::class);
    }
}

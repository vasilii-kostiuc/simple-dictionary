<?php

namespace App\Domain\Dictionary\Models;

use App\Domain\Language\Models\Language;
use App\Models\User;
use Database\Factories\DictionaryFactory as DictionaryFactoryAlias;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dictionary extends Model
{
    /** @use HasFactory<\Database\Factories\DictionaryFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'language_from_id', 'language_to_id'];

    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function languageFrom(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_from_id');
    }

    public function languageTo(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_to_id');
    }

    protected static function newFactory(): Factory
    {
        return DictionaryFactoryAlias::new();
    }
}

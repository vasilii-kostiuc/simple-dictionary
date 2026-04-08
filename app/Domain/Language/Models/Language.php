<?php

namespace App\Domain\Language\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /** @use HasFactory<\Database\Factories\LanguageFactory> */
    use HasFactory;

    protected $fillable = ['name', 'code', 'icon'];

    protected static function newFactory(): Factory
    {
        return LanguageFactory::new();
    }

}

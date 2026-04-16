<?php

namespace App\Domain\User\Models;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Match\Models\MatchUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'current_dictionary',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function currentDictionary(): HasOne
    {
        return $this->hasOne(Dictionary::class, 'id', 'current_dictionary');
    }

    public function dictionaries(): HasMany
    {
        return $this->hasMany(Dictionary::class, 'user_id', 'id');
    }

    public function matchUsers(): HasMany
    {
        return $this->hasMany(MatchUser::class, 'user_id', 'id');
    }

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}

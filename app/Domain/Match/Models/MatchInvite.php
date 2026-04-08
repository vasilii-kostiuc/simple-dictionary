<?php

namespace App\Domain\Match\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchInvite extends Model
{
    protected $fillable = [
        'token',
        'created_by_user_id',
        'participants_limit',
        'status',
        'payload',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function resolvedStatus(): string
    {
        if ($this->status === 'active' && $this->expires_at?->isPast()) {
            return 'expired';
        }

        return $this->status;
    }
}

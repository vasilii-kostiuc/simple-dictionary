<?php

namespace App\Domain\Match\Models;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Match\Enums\{MatchStatus, MatchType, MatchCompletionReason};
use App\Domain\Match\Events\MatchCompletedEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Illuminate\Support\Collection;

class MatchModel extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'dictionary_id',
        'match_type',
        'match_type_params',
        'status',
        'started_at',
        'completed_at',
        'completion_reason',
        'completion_details'
    ];

    protected $casts = [
        'match_type' => MatchType::class,
        'match_type_params' => 'array',
        'status' => MatchStatus::class,
        'completion_reason' => MatchCompletionReason::class,
        'completion_details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function dictionary(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'match_users')
            ->withPivot('score', 'answered_count', 'correct_answers_count', 'place', 'is_winner')
            ->withTimestamps();
    }

    public function matchUsers(): HasMany
    {
        return $this->hasMany(MatchUser::class, 'match_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(MatchStep::class, 'match_id');
    }

    public function getStepsForParticipant(?int $userId, ?string $guestId): Collection
    {
        return $this->steps()
            ->where(function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            })
            ->get();
    }

    public function getParticipantScore(?int $userId, ?string $guestId): int
    {
        return $this->matchUsers()
            ->where(function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            })
            ->first()?->score ?? 0;
    }

    public function completeMatch(MatchCompletionReason $reason, ?array $details = null): void
    {
        $this->status = MatchStatus::Completed;
        $this->completed_at = now();
        $this->completion_reason = $reason;
        $this->completion_details = $details;

        $this->determineWinner();

        $this->save();

        event(new MatchCompletedEvent($this));
    }

    private function determineWinner(): void
    {
        $matchUsers = $this->matchUsers()
            ->orderByDesc('score')
            ->orderBy('answered_count')
            ->get();

        $place = 1;
        foreach ($matchUsers as $matchUser) {
            $matchUser->place = $place++;
            $matchUser->is_winner = $matchUser->place === 1;
            $matchUser->save();
        }
    }
}

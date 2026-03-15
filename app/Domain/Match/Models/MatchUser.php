<?php

namespace App\Domain\Match\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MatchUser extends Model
{
    protected $fillable = [
        'match_id',
        'user_id',
        'guest_id',
        'participant_name',
        'participant_avatar',
        'score',
        'answered_count',
        'correct_answers_count',
        'place',
        'is_winner',
        'joined_at',
        'left_at'
    ];

    protected $casts = [
        'is_winner' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function isAuthenticated(): bool
    {
        return $this->user_id !== null;
    }

    public function getIdentifier(): string
    {
        return (string)($this->user_id ?? $this->guest_id);
    }

    public static function fromUser(User $user, int $matchId): self
    {
        return self::create([
            'match_id' => $matchId,
            'user_id' => $user->id,
            'guest_id' => null,
            'participant_name' => $user->name,
            'participant_avatar' => $user->avatar ?? null,
            'joined_at' => now(),
        ]);
    }

    public static function fromGuest(string $guestId, int $matchId, ?string $name = null): self
    {
        return self::create([
            'match_id' => $matchId,
            'user_id' => null,
            'guest_id' => $guestId,
            'participant_name' => $name ?? self::generateGuestName(),
            'participant_avatar' => self::generateGuestAvatar($guestId),
            'joined_at' => now(),
        ]);
    }

    private static function generateGuestName(): string
    {
        $adjectives = ['Quick', 'Smart', 'Clever', 'Brave', 'Swift', 'Wise', 'Bold', 'Fast'];
        $animals = ['Fox', 'Wolf', 'Eagle', 'Hawk', 'Lion', 'Tiger', 'Bear', 'Falcon'];

        return $adjectives[array_rand($adjectives)] . ' ' .
            $animals[array_rand($animals)] . ' ' .
            rand(100, 999);
    }

    private static function generateGuestAvatar(string $guestId): string
    {
        return 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $guestId;
    }

    public function incrementScore(bool $isCorrect): void
    {
        $this->answered_count++;

        if ($isCorrect) {
            $this->score++;
            $this->correct_answers_count++;
        }

        $this->save();
    }
}

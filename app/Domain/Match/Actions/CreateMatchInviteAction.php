<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\Models\MatchInvite;
use App\Domain\User\Models\User;
use Illuminate\Support\Str;

class CreateMatchInviteAction
{
    public function handle(array $data, ?User $user): MatchInvite
    {
        return MatchInvite::create([
            'token' => (string) Str::ulid(),
            'created_by_user_id' => $user?->id,
            'participants_limit' => $data['participants_limit'] ?? 2,
            'status' => 'pending',
            'payload' => [
                'language_from_id' => $data['language_from_id'],
                'language_to_id' => $data['language_to_id'],
                'dictionary_id' => $data['dictionary_id'] ?? null,
                'match_type' => $data['match_type'],
                'match_type_params' => $data['match_type_params'],
            ],
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }
}

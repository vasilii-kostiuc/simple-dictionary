<?php

namespace App\Domain\Match\Service;

use App\Domain\Match\Enums\{MatchStatus, MatchCompletionReason};
use App\Domain\Match\Events\{MatchCreatedEvent, MatchStartedEvent};
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchUser;
use App\Models\User;

class MatchService
{
    public function create(array $data, array $participants): MatchModel
    {
        $match = MatchModel::create([
            'dictionary_id' => $data['dictionary_id'],
            'match_type' => $data['match_type'],
            'match_type_params' => $data['match_type_params'],
            'status' => MatchStatus::New,
        ]);

        // $participants = [
        //   ['type' => 'user', 'id' => 123],
        //   ['type' => 'guest', 'id' => 'uuid-guest-1', 'name' => 'Guest Name'],
        // ]

        foreach ($participants as $participant) {
            if ($participant['type'] === 'user') {
                $user = User::find($participant['id']);
                if ($user) {
                    MatchUser::fromUser($user, $match->id);
                }
            } else {
                MatchUser::fromGuest(
                    $participant['id'],
                    $match->id,
                    $participant['name'] ?? null
                );
            }
        }

        event(new MatchCreatedEvent($match));

        return $match;
    }

    public function start(MatchModel $match): MatchModel
    {
        if ($match->status === MatchStatus::InProgress) {
            return $match;
        }

        $match->status = MatchStatus::InProgress;
        $match->started_at = now();
        $match->save();

        event(new MatchStartedEvent($match));

        return $match;
    }

    public function complete(MatchModel $match, ?MatchCompletionReason $reason = null, ?array $details = []): MatchModel
    {
        if ($reason === null) {
            $reason = MatchCompletionReason::defaultForMatchType($match->match_type);
        }

        $match->completeMatch($reason, $details);

        return $match;
    }
}

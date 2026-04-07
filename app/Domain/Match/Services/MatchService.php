<?php

namespace App\Domain\Match\Services;

use App\Domain\Match\Enums\MatchCompletionReason;
use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Events\MatchCreatedEvent;
use App\Domain\Match\Events\MatchStartedEvent;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchUser;
use App\Domain\User\Models\User;

class MatchService
{
    public function __construct(
        private readonly MatchStepService $matchStepService
    ) {
    }

    public function create(array $data, array $participants): MatchModel
    {
        $match = MatchModel::create([
            'language_from_id' => $data['language_from_id'],
            'language_to_id' => $data['language_to_id'],
            'dictionary_id' => $data['dictionary_id'] ?? null,
            'match_type' => $data['match_type'],
            'match_type_params' => $data['match_type_params'],
            'status' => MatchStatus::New,
        ]);

        foreach ($participants as $participant) {
            if ($participant['type'] === 'user') {
                $user = User::find($participant['id']);
                if ($user !== null) {
                    MatchUser::fromUser($user, $match->id);
                }

                continue;
            }

            MatchUser::fromGuest(
                $participant['id'],
                $match->id,
                $participant['name'] ?? null
            );
        }

        $match->refresh();
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

        foreach ($match->matchUsers as $matchUser) {
            $this->matchStepService->generateNextStepForParticipant(
                $match,
                $matchUser->user_id,
                $matchUser->guest_id
            );
        }

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

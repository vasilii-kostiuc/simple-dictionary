<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Domain\Match\Enums\{MatchStatus, MatchCompletionReason};
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Service\{MatchService, MatchStepService};
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\CreateMatchRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Match\{MatchResource, MatchSummaryResource};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class MatchController extends Controller
{
    private const MATCH_CAN_BE_STARTED_ONLY_IN_NEW_STATE = 'match_can_be_started_only_in_new_state';

    public function __construct(
        private MatchService $matchService,
        private MatchStepService $matchStepService
    ) {}

    public function index(Request $request)
    {
        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        $matches = QueryBuilder::for(MatchModel::class)
            ->select('matches.*')
            ->allowedFilters(['status'])
            ->where(function ($q) use ($userId, $guestId) {
                $q->whereHas('matchUsers', function ($q) use ($userId, $guestId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } elseif ($guestId) {
                        $q->where('guest_id', $guestId);
                    }
                });
            })
            ->orderBy('started_at', 'DESC')
            ->with('matchUsers')
            ->get();

        return new ApiResponseResource([
            'data' => MatchResource::collection($matches)
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(CreateMatchRequest $request)
    {
        info(__METHOD__ , $request->validated());
        $match = $this->matchService->create(
            $request->validated(),
            $request->input('participants')
        );

        $match = $this->matchService->start($match);

        $match->load('matchUsers', 'steps');

        return new ApiResponseResource([
            'data' => new MatchResource($match),
            'message' => 'Match created successfully'
        ])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MatchModel $match)
    {
        $match->load('matchUsers', 'steps');

        return new ApiResponseResource([
            'data' => new MatchResource($match)
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function start(MatchModel $match)
    {
        if ($match->status !== MatchStatus::New) {
            return new ApiResponseResource([
                'message' => 'Match already started',
                'errors' => [
                    self::MATCH_CAN_BE_STARTED_ONLY_IN_NEW_STATE => 'Match can be started only in new state'
                ]
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $this->matchService->start($match);

        return new ApiResponseResource([
            'message' => 'Match started successfully',
            'data' => new MatchResource($match->load('matchUsers'))
        ]);
    }

    public function complete(MatchModel $match, Request $request)
    {
        if ($match->status === MatchStatus::Completed) {
            return new ApiResponseResource([
                'message' => 'Match already completed'
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $reason = $request->input('reason')
            ? MatchCompletionReason::from($request->input('reason'))
            : null;

        $this->matchService->complete($match, $reason);

        return new ApiResponseResource([
            'message' => 'Match completed successfully',
            'data' => new MatchResource($match->load('matchUsers'))
        ]);
    }

    public function getActiveMatch(Request $request)
    {
        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        if (!$userId && !$guestId) {
            return new ApiResponseResource([
                'data' => null,
                'message' => 'No active match found'
            ])->response()->setStatusCode(Response::HTTP_OK);
        }

        $match = MatchModel::where(function ($q) use ($userId, $guestId) {
            $q->whereHas('matchUsers', function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            });
        })
            ->where('status', MatchStatus::InProgress)
            ->with(['matchUsers', 'steps'])
            ->first();

        return new ApiResponseResource([
            'data' => $match ? new MatchResource($match) : null
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function summary(MatchModel $match)
    {
        $matchTime = $match->completed_at
            ? $match->started_at->diffInSeconds($match->completed_at)
            : null;

        $participants = $match->matchUsers->map(function ($matchUser) use ($match) {
            $stepsCount = $match->steps()
                ->where(function ($q) use ($matchUser) {
                    if ($matchUser->user_id) {
                        $q->where('user_id', $matchUser->user_id);
                    } else {
                        $q->where('guest_id', $matchUser->guest_id);
                    }
                })
                ->count();

            return [
                'participant_name' => $matchUser->participant_name,
                'participant_avatar' => $matchUser->participant_avatar,
                'score' => $matchUser->score,
                'answered_count' => $matchUser->answered_count,
                'correct_answers_count' => $matchUser->correct_answers_count,
                'steps_count' => $stepsCount,
                'place' => $matchUser->place,
                'is_winner' => $matchUser->is_winner,
                'is_guest' => $matchUser->isGuest(),
            ];
        });

        $winner = $match->matchUsers->where('is_winner', true)->first();

        return new ApiResponseResource([
            'data' => new MatchSummaryResource((object)[
                'match_id' => $match->id,
                'match_time_seconds' => $matchTime,
                'participants' => $participants,
                'winner' => $winner ? [
                    'participant_name' => $winner->participant_name,
                    'score' => $winner->score,
                ] : null,
                'completion_reason' => $match->completion_reason?->value,
                'started_at' => $match->started_at,
                'completed_at' => $match->completed_at,
            ])
        ])->response()->setStatusCode(Response::HTTP_OK);
    }
}

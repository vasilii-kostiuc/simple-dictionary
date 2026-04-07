<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Domain\Match\Enums\{MatchStatus, MatchCompletionReason, MatchType};
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Services\{MatchService, MatchSummaryBuilder};
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
        private MatchSummaryBuilder $matchSummaryBuilder
    ) {
    }

    public function index(Request $request)
    {
        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        if (! $userId && ! $guestId) {
            return new ApiResponseResource([
                'data' => []
            ])->response()->setStatusCode(Response::HTTP_OK);
        }

        $matches = QueryBuilder::for(MatchModel::class)
            ->select('matches.*')
            ->allowedFilters(['status'])
            ->where(function ($q) use ($userId, $guestId) {
                $q->whereHas('matchUsers', function ($q) use ($userId, $guestId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
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
        info(__METHOD__, $request->validated());
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
            ? MatchCompletionReason::tryFrom($request->input('reason'))
            : null;

        $this->matchService->complete($match, $reason);

        return new ApiResponseResource([
            'message' => 'Match completed successfully',
            'data' => new MatchResource($match->load('matchUsers'))
        ]);
    }

    public function expire(MatchModel $match)
    {
        if ($match->match_type === MatchType::Time) {
            $this->matchService->complete($match, MatchCompletionReason::TimeExpired);
            $match->refresh();
            return new ApiResponseResource(['message' => 'Match completed successfully', 'data' => new MatchResource($match)]);
        }

        return new ApiResponseResource(['success' => false, 'message' => 'Training expiration is not supported for tris training type'])->response()->setStatusCode(Response::HTTP_CONFLICT);
    }

    public function getActiveMatch(Request $request)
    {
        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        if (! $userId && ! $guestId) {
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
        return new ApiResponseResource([
            'data' => new MatchSummaryResource($this->matchSummaryBuilder->build($match))
        ])->response()->setStatusCode(Response::HTTP_OK);
    }
}

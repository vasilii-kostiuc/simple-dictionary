<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Domain\Match\Enums\MatchStatus;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Service\MatchStepService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Match\MatchStepResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MatchStepController extends Controller
{
    private const ERROR_MATCH_FINISHED = 'match_finished';
    private const ERROR_STEP_NOT_COMPLETED = 'previous_step_not_completed';
    private const ERROR_CURRENT_STEP_NOT_FOUND = 'current_step_not_found';
    private const ERROR_PARTICIPANT_REQUIRED = 'participant_required';

    public function __construct(
        private MatchStepService $matchStepService
    ) {
    }

    public function show(MatchModel $match, MatchStep $step)
    {
        return ApiResponseResource::make([
            'data' => new MatchStepResource($step)
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function next(MatchModel $match, Request $request)
    {
        if ($match->status === MatchStatus::Completed) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_MATCH_FINISHED => 'Match is finished'],
                'message' => 'Match is finished',
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        if (! $userId && ! $guestId) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_PARTICIPANT_REQUIRED => 'User ID or Guest ID is required'],
                'message' => 'Participant identification required',
            ])->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        // Проверяем последний шаг для участника
        $lastStep = $match->steps()
            ->where(function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            })
            ->orderBy('step_number', 'desc')
            ->first();

        if ($lastStep && ! $lastStep->isPassedOrSkipped() && ! $lastStep->hasAttempts()) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_STEP_NOT_COMPLETED => 'Previous step has not been attempted'],
                'message' => 'New step can be created only after attempting previous step',
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $nextStep = $this->matchStepService->generateNextStepForParticipant(
            $match,
            $userId,
            $guestId
        );

        return ApiResponseResource::make([
            'data' => new MatchStepResource($nextStep),
            'message' => 'Next step generated successfully'
        ]);
    }

    public function current(MatchModel $match, Request $request)
    {
        if ($match->status === MatchStatus::Completed) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_MATCH_FINISHED => 'Match is finished'],
                'message' => 'Match is finished',
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        if (! $userId && ! $guestId) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_PARTICIPANT_REQUIRED => 'User ID or Guest ID is required'],
                'message' => 'Participant identification required',
            ])->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $currentStep = $match->steps()
            ->where(function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            })
            ->where('skipped', false)
            ->whereDoesntHave('attempts', fn ($q) => $q->where('is_correct', true))
            ->orderBy('step_number', 'desc')
            ->first();

        if (! $currentStep) {
            return new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_CURRENT_STEP_NOT_FOUND => 'Current step not found'],
                'message' => 'No current step found for this participant',
            ])->response()->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return ApiResponseResource::make([
            'data' => new MatchStepResource($currentStep)
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function skip(MatchModel $match, MatchStep $step, Request $request)
    {
        $userId = $request->user()?->id;
        $guestId = $request->input('guest_id');

        // Проверяем что шаг принадлежит участнику
        if (($userId && $step->user_id !== $userId) ||
            ($guestId && $step->guest_id !== $guestId)
        ) {
            return new ApiResponseResource([
                'success' => false,
                'message' => 'Step does not belong to this participant',
            ])->response()->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $this->matchStepService->skip($step);

        return ApiResponseResource::make([
            'data' => new MatchStepResource($step),
            'message' => 'Step skipped successfully'
        ]);
    }
}

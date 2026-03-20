<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Service\MatchStepAttemptService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\SubmitMatchAnswerRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Match\{MatchStepAttemptResource, MatchStepResource};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MatchStepAttemptController extends Controller
{
    public function __construct(
        private MatchStepAttemptService $matchStepAttemptService
    ) {}

    public function index(Request $request, MatchModel $match, MatchStep $step): JsonResponse
    {
        if ($request->has('is_correct')) {
            $isCorrect = filter_var($request->input('is_correct'), FILTER_VALIDATE_BOOLEAN);
            $attempts = $step->attempts()->where('is_correct', $isCorrect)->get();
        } else {
            $attempts = $step->attempts()->get();
        }

        return new ApiResponseResource([
            'data' => MatchStepAttemptResource::collection($attempts)
        ])->response();
    }

    public function store(MatchModel $match, MatchStep $step, SubmitMatchAnswerRequest $request): JsonResponse
    {
        // Проверяем что участник имеет право отвечать на этот шаг
        $userId = $request->user()?->id;
        $guestId = $request->input('participant_id');
        $participantType = $request->input('participant_type');

        if ($participantType === 'user' && $step->user_id !== $userId) {
            return new ApiResponseResource([
                'success' => false,
                'message' => 'Step does not belong to this user',
            ])->response()->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if ($participantType === 'guest' && $step->guest_id !== $guestId) {
            return new ApiResponseResource([
                'success' => false,
                'message' => 'Step does not belong to this guest',
            ])->response()->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if ($step->isPassed()) {
            return new ApiResponseResource([
                'success' => false,
                'message' => 'Match step is already passed',
                'errors' => ['match_step_already_passed' => 'Match step is already passed']
            ])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $attempt = $this->matchStepAttemptService->submitAnswer(
            $step,
            $request->input('attempt_data'),
            $request->input('attempt_number')
        );

        // Загружаем обновленный шаг
        $step->refresh();

        return ApiResponseResource::make([
            'data' => [
                'attempt' => new MatchStepAttemptResource($attempt),
                'step' => new MatchStepResource($step),
            ],
            'message' => $attempt->is_correct ? 'Correct answer!' : 'Incorrect answer'
        ])->response();


    }
}

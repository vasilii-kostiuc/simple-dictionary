<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Domain\Training\Factories\CompletionConditionFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStep;
use App\Domain\Training\Service\TrainingStepAttemptService;
use App\Domain\Training\Service\TrainingStepProgressService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingStepAttemptResource;
use App\Http\Resources\Training\TrainingStepProgressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrainingStepAttemptController extends Controller
{
    private TrainingStepAttemptService $trainingStepAttemptService;
    private CompletionConditionFactory $completionConditionFactory;
    private TrainingStepProgressService $trainingStepProgressService;

    public function __construct(TrainingStepAttemptService $trainingStepAttemptService, CompletionConditionFactory $completionConditionFactory, TrainingStepProgressService $trainingStepProgressService)
    {
        $this->trainingStepAttemptService = $trainingStepAttemptService;
        $this->completionConditionFactory = $completionConditionFactory;
        $this->trainingStepProgressService = $trainingStepProgressService;
    }

    public function index(Request $request, Training $training, TrainingStep $step): JsonResponse
    {
        if ($request->has('is_correct')) {
            $isCorrect = filter_var($request->input('is_correct'), FILTER_VALIDATE_BOOLEAN);
            $attempts = $step->attempts()->where('is_correct', $isCorrect)->get();
        } else {
            $attempts = $step->attempts()->get();
        }

        return new ApiResponseResource(['data' => TrainingStepAttemptResource::collection($attempts)])->response();
    }

    public function store(Training $training, TrainingStep $step, Request $request): JsonResponse
    {
        //sleep(1);
        if ($step->isPassed()) {
            return new ApiResponseResource(['success' => false, 'message' => 'Training step is passed, there is unposible to attempt already passed step', 'data' => null, 'errors' => ['training_step_is_already_passed' => 'Training step is passed']])
                ->response()
                ->setStatusCode(Response::HTTP_CONFLICT);
        }

        $attemptData = $request->input('attempt_data') ?? [];
        $attempt = $this->trainingStepAttemptService->create($step, $attemptData);

        $completionCondition = $this->completionConditionFactory->create($training);

        if ($completionCondition->isCompleted()) {
            $training->completeTraining();
        }

        $progress = $this->trainingStepProgressService->getProgress($step);

        return ApiResponseResource::make(['data' => new TrainingStepAttemptResource($attempt)])->response();
    }

}

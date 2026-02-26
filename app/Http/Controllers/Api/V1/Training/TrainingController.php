<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Domain\Training\Enums\TrainingCompletionReason;
use App\Domain\Training\Enums\TrainingCompletionType;
use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Enums\TrainingType;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Service\TrainingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingResource;
use App\Http\Resources\Training\TrainingSummaryResource;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class TrainingController extends Controller
{
    private const TRAINING_CAN_BE_STARTED_ONLY_IN_NEW_STATE = 'training_can_be_started_only_in_new_state';

    private TrainingService $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function index()
    {
        $trainings = QueryBuilder::for(Training::class)
            ->allowedFilters(['status'])
            ->orderBy('started_at', 'DESC')->get();
        return new ApiResponseResource(['data' => TrainingResource::collection($trainings)])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new ApiResponseResource(['data' => new TrainingResource($training)])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Training $training)
    {
        return new ApiResponseResource(['data' => $training])->response()->setStatusCode(Response::HTTP_OK);
    }

    public function start(Training $training)
    {
        if ($training->status !== TrainingStatus::New) {
            return new ApiResponseResource(['message' => 'Training already started', 'errors' => [self::TRAINING_CAN_BE_STARTED_ONLY_IN_NEW_STATE => 'Training can be started only in new state']]);
        }

        $this->trainingService->start($training);
        return new ApiResponseResource(['message' => 'Training started successfully', 'data' => new TrainingResource($training)]);
    }

    public function expire(Training $training)
    {
        if ($training->completion_type === TrainingCompletionType::Time) {
            $this->trainingService->complete($training, TrainingCompletionReason::Expired);
            return new ApiResponseResource(['message' => 'Training completed successfully', 'data' => new TrainingResource($training)]);
        }

        return new ApiResponseResource(['success' => false, 'message' => 'Training expiration is not supported for tris training type'])->response()->setStatusCode(Response::HTTP_CONFLICT);
    }

    public function terminate(Training $training)
    {
        if ($training->status === TrainingStatus::Completed) {
            return new ApiResponseResource(['message' => 'Training already compleated'])->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $this->trainingService->complete($training, TrainingCompletionReason::Terminated);

        return new ApiResponseResource(['success'=>false, 'message' => 'Training terminated successfully', 'data' => new TrainingResource($training)]);
    }


    public function summary(Training $training)
    {
        $trainingTime = $training->completed_at ? $training->started_at->diffInSeconds($training->completed_at) : null;
        $stepsCount = $training->steps()->count();
        $correctAnswersCount = $training->stepAttempts()->where('is_correct', true)->select('id')->distinct()->count();
        $skippedStepsCount = $training->steps()->where('skipped', true)->count();
        $completionReason = $training->completion_reason?->name;

        return new ApiResponseResource([
            'data' => new TrainingSummaryResource((object)[
                'training_time_seconds' => $trainingTime,
                'steps_count' => $stepsCount,
                'correct_answers_count' => $correctAnswersCount,
                'skipped_steps_count' => $skippedStepsCount,
                'completion_reason' => $completionReason,
                'started_at' => $training->started_at,
                'completed_at' => $training->completed_at,
            ])
        ])->response()->setStatusCode(Response::HTTP_OK);
    }
}

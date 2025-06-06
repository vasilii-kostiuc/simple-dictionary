<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingResource;
use App\Http\Resources\Training\TrainingStepAttemptResource;
use App\Http\Resources\Training\TrainingStepResource;
use App\Training\Enums\TrainingStatus;
use App\Training\Factories\CompletionConditionFactory;
use App\Training\Factories\TrainingStrategyFactory;
use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Service\StepCheckService;
use App\Training\Service\TrainingService;
use App\Training\Service\TrainingStepAttemptService;
use App\Training\Service\TrainingStepService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrainingController extends Controller
{
    private const ERROR_TRAINING_FINISHED = 'training_finished';
    private const ERROR_STEP_NOT_COMPLETED = 'previous_step_not_completed';

    private TrainingService $trainingService;


    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new ApiResponseResource(['data' => new TrainingResource($training)])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function start(Training $training)
    {
        if (TrainingStatus::from((int)$training->status) !== TrainingStatus::New) {
            return new ApiResponseResource(['message' => 'Training already started', 'errors' => ['training_can_be_started_only_in_new_state' => 'Training can be started only in new state']]);
        }

        $this->trainingService->start($training);
        return new ApiResponseResource(['message' => 'Training started successfully', 'data' => new TrainingResource($training)]);
    }

}

<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\TrainingResource;
use App\Http\Resources\TrainingStepAttemptResource;
use App\Http\Resources\TrainingStepResource;
use App\Training\Enums\TrainingStatus;
use App\Training\Factories\TrainingStrategyFactory;
use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Service\StepCheckService;
use App\Training\Service\TrainingService;
use App\Training\Service\TrainingStepAttemptService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    private TrainingService $trainingService;
    private StepCheckService $stepCheckService;
    private TrainingStepAttemptService $trainingStepAttemptService;

    private TrainingStrategyFactory $trainingStrategyFactory;
    public function __construct(TrainingStrategyFactory $trainingStrategyFactory, TrainingService $trainingService, TrainingStepAttemptService $trainingStepAttemptService, StepCheckService $stepCheckService)
    {
        $this->trainingStrategyFactory = $trainingStrategyFactory;
        $this->trainingService = $trainingService;
        $this->trainingStepAttemptService = $trainingStepAttemptService;
        $this->stepCheckService = $stepCheckService;
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new TrainingResource($training);
    }

    public function nextStep(Training $training)
    {
        if ($training->status == TrainingStatus::Finished) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'error' => 'training_finished',
                    'message' => 'Training is finished',
                ]))->response()->setStatusCode(409);
        }

        if ($this->trainingService->isLastStepCompletedOrSkipped($training)) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'error' => 'previous_step_not_completed',
                    'message' => 'New step can be created only after compliting prev step',
                ]))->response()->setStatusCode(409);
        }

        $nextStep = $this->trainingStrategyFactory->create($training)->generateNextStep();

        return ApiResponseResource::make(['data' => new TrainingStepResource($nextStep), 'message' => 'Next step generated successfully']);
    }

    public function completeStep(TrainingStep $step, Request $request)
    {
        $attemptData = $request->all('attempt_data');

        $isPassed = $this->stepCheckService->check($step, $attemptData);

        $attempt = $this->trainingStepAttemptService->create($step->id, $attemptData, $isPassed);

        return new TrainingStepAttemptResource($attempt);
    }
}

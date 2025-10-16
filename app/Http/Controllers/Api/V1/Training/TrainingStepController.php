<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Factories\TrainingStrategyFactory;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStep;
use App\Domain\Training\Service\StepCheckService;
use App\Domain\Training\Service\TrainingStepProgressService;
use App\Domain\Training\Service\TrainingStepService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingStepProgressResource;
use App\Http\Resources\Training\TrainingStepResource;
use Illuminate\Http\Response;

class TrainingStepController extends Controller
{
    private const ERROR_TRAINING_FINISHED = 'training_finished';
    private const ERROR_STEP_NOT_COMPLETED = 'previous_step_not_completed';
    private const ERROR_CURRENT_STEP_NOT_FOUND = 'current_step_not_found';
    private TrainingStrategyFactory $trainingStrategyFactory;
    private TrainingStepService $trainingStepService;
    private TrainingStepProgressService $trainingStepProgressService;

    public function __construct(
        TrainingStrategyFactory     $trainingStrategyFactory,
        StepCheckService            $stepCheckService,
        TrainingStepService         $trainingStepService,
        TrainingStepProgressService $trainingStepProgressService,
    )
    {
        $this->trainingStrategyFactory = $trainingStrategyFactory;
        $this->trainingStepService = $trainingStepService;
        $this->trainingStepProgressService = $trainingStepProgressService;
    }

    public function show(Training $training, TrainingStep $step)
    {
        return ApiResponseResource::make(['data' =>new TrainingStepResource($step)])->response()->setStatusCode(Response::HTTP_OK);;
    }

    public function next(Training $training)
    {
        sleep(3);
        if ($training->status == TrainingStatus::Completed) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'errors' => [self::ERROR_TRAINING_FINISHED => 'Training is finished'],
                    'message' => 'Training is finished',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $lastStep = $training->lastStep();
        if ($lastStep && !$lastStep->isPassedOrSkipped()) {
            return (new ApiResponseResource(
                [
                    'success' => false,
                    'errors' => [self::ERROR_STEP_NOT_COMPLETED => 'Previous step is not completed'],
                    'message' => 'New step can be created only after compliting prev step',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);;
        }

        $generatedStep = $this->trainingStrategyFactory->create($training)->generateNextStep();

        $nextStep = $this->trainingStepService->create($generatedStep, $training);
        return ApiResponseResource::make(['data' => new TrainingStepResource($nextStep), 'message' => 'Next step generated successfully']);
    }

    public function current(Training $training)
    {
        sleep(3);
        if ($training->status == TrainingStatus::Completed) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'errors' => [self::ERROR_TRAINING_FINISHED => 'Training is finished'],
                    'message' => 'Training is finished',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);
        }
        $currentStep = $training->lastStep();

        if ($currentStep === null) {
            return (new ApiResponseResource([
                'success' => false,
                'errors' => [self::ERROR_CURRENT_STEP_NOT_FOUND => 'Current step not found'],
                'message' => 'Current step not found',
            ]))->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        return ApiResponseResource::make(['data' => new TrainingStepResource($currentStep)]);
    }

    public function progress(Training $training, TrainingStep $step)
    {
        sleep(2);
        $progress = $this->trainingStepProgressService->getProgress($step);

        return ApiResponseResource::make(['data' => new TrainingStepProgressResource($progress)]);
    }

}

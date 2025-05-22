<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Training\Models\TrainingStep;
use App\Training\Service\TrainingService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    private TrainingService $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new TrainingResource($training);
    }

    public  function completeStep(TrainingStep $trainingStep, Request $request){
        $this->trainingService->completeStep($trainingStep);
    }
}

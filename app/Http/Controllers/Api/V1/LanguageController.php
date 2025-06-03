<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\LanguageResource;
use App\Models\Language;

class LanguageController extends Controller
{
    public function index()
    {
        return new ApiResponseResource(['data' => LanguageResource::collection(Language::all())]);
    }

    public function show(Language $language)
    {
        return new ApiResponseResource(['data' => new LanguageResource($language)]);
    }
}

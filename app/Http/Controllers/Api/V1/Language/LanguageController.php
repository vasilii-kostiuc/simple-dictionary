<?php

namespace App\Http\Controllers\Api\V1\Language;

use App\Domain\Language\Models\Language;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Language\LanguageResource;

class LanguageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/languages",
     *     tags={"Languages"},
     *     summary="Get list of categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *     )
     * )
     */
    public function index()
    {
        return new ApiResponseResource(['data' => LanguageResource::collection(Language::all())]);
    }

    public function show(Language $language)
    {
        return new ApiResponseResource(['data' => new LanguageResource($language)]);
    }
}

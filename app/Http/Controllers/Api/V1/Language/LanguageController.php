<?php

namespace App\Http\Controllers\Api\V1\Language;

use App\Core\Language\Models\Language;
use App\Core\Shared\Cache\CacheInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Language\LanguageResource;

class LanguageController extends Controller
{
    public function __construct(private readonly CacheInterface $cache) {}

    public function index()
    {
        $languages = $this->cache->rememberForever(
            'languages.all',
            fn () => Language::all()
        );

        return new ApiResponseResource(['data' => LanguageResource::collection($languages)]);
    }

    public function show(Language $language)
    {
        return new ApiResponseResource(['data' => new LanguageResource($language)]);
    }
}

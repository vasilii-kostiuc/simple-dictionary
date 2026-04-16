<?php

namespace App\Http\Controllers\Api\V1\Dictionary;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Dictionary\Services\DictionaryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dictionary\StoreDictionaryRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Dictionary\DictionaryResource;
use Illuminate\Http\Response;

class DictionaryController extends Controller
{
    public function __construct(DictionaryService $dictionaryService)
    {
        $this->dictionaryService = $dictionaryService;
    }

    public function index()
    {
        $dictionaries = Dictionary::all();

        return new ApiResponseResource(['data' => DictionaryResource::collection($dictionaries)]);
    }

    public function show(Dictionary $dictionary)
    {
        return new ApiResponseResource(['data' => new DictionaryResource($dictionary)]);
    }

    public function store(StoreDictionaryRequest $request)
    {
        $dictionary = $this->dictionaryService->create($request->validated() + ['user_id' => auth()->id()]);

        return new ApiResponseResource(['data' => new DictionaryResource($dictionary)])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Dictionary $dictionary)
    {
        $this->dictionaryService->delete($dictionary);

        return new ApiResponseResource(['data' => null])->response()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}

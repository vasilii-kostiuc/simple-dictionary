<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDictionaryRequest;
use App\Http\Resources\DictionaryResource;
use App\Models\Dictionary;
use App\Service\DictionaryService;

class DictionaryController extends Controller
{
    public function __construct(DictionaryService $dictionaryService)
    {
        $this->dictionaryService = $dictionaryService;
    }

    public function index()
    {
        $dictionaries = Dictionary::all();

        return DictionaryResource::collection($dictionaries);
    }

    public function show(Dictionary $dictionary)
    {
        return new DictionaryResource($dictionary);
    }

    public function store(StoreDictionaryRequest $request)
    {
        $dictionary = $this->dictionaryService->create($request->validated() + ['user_id' => auth()->id()]);

        return new DictionaryResource($dictionary);
    }

    public function destroy(Dictionary $dictionary)
    {
        $this->dictionaryService->delete($dictionary);

        return response()->json(null, 204);
    }
}

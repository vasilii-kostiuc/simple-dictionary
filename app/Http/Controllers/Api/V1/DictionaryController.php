<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DictionaryResource;
use App\Models\Dictionary;
use App\Service\DictionaryService;
use Illuminate\Support\Facades\Http;

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
        $dictionary = $this->dictionaryService->create($request->validated());

        return new DictionaryResource($dictionary);
    }

    public function destroy(Dictionary $dictionary)
    {
        $dictionary->delete();

        return response()->json(null, 204);
    }

}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DictionaryResource;
use App\Models\Dictionary;
use App\Service\DictionaryService;

class DictionaryController extends Controller
{

    public function __construct(DictionaryService $dictionaryService){
        $this->dictionaryService = $dictionaryService;
    }

    public function index(){
        $dictionaries  = Dictionary::all();

        return DictionaryResource::collection($dictionaries);
    }

}

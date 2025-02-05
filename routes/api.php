<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('languages', [\App\Http\Controllers\Api\LanguageController::class, 'index']);
Route::get('languages/{language}', [\App\Http\Controllers\Api\LanguageController::class, 'show']);

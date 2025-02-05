<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')/*->middleware('auth:sanctum')*/->group(
    function () {
        Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class);

        Route::get('languages', [\App\Http\Controllers\Api\V1\LanguageController::class, 'index']);
        Route::get('languages/{language}', [\App\Http\Controllers\Api\V1\LanguageController::class, 'show']);


        Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');
    });

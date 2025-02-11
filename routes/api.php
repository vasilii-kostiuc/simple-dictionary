<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')/*->middleware('auth:sanctum')*/ ->group(function () {
    Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class)->name('auth.register');
    Route::post('auth/login', \App\Http\Controllers\Api\V1\Auth\LoginController::class)->name('auth.login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('languages', [\App\Http\Controllers\Api\V1\LanguageController::class, 'index'])->name('languages.index');
        Route::get('languages/{language}', [\App\Http\Controllers\Api\V1\LanguageController::class, 'show'])->name('languages.show');

        Route::get('dictionaries', [\App\Http\Controllers\Api\V1\DictionaryController ::class, 'index'])->name('dictionaries.index');
        Route::post('dictionaries', [\App\Http\Controllers\Api\V1\DictionaryController ::class, 'store'])->name('dictionaries.store');
        Route::get('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\DictionaryController ::class, 'show'])->name('dictionaries.show');
        Route::delete('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\DictionaryController ::class, 'destroy'])->name('dictionaries.destroy');
    });
});

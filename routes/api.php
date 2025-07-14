<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('publish', function () {
    Redis::publish('test-channel', json_encode([
        'name' => 'Adam Wathan',
    ]));
});

Route::get('subscribe', function () {
    Redis::psubscribe(['*'], function (string $message, string $channel) {
        echo $message;
    });
});

Route::prefix('v1')->group(function () {
    Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class)->name('auth.register');
    Route::post('auth/login', \App\Http\Controllers\Api\V1\Auth\LoginController::class)->name('auth.login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'update'])->name('profile.update');

        Route::post('auth/logout', \App\Http\Controllers\Api\V1\Auth\LogoutController::class)->name('auth.logout');

        Route::get('languages', [\App\Http\Controllers\Api\V1\Language\LanguageController::class, 'index'])->name('languages.index');
        Route::get('languages/{language}', [\App\Http\Controllers\Api\V1\Language\LanguageController::class, 'show'])->name('languages.show');

        Route::get('dictionaries', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'index'])->name('dictionaries.index');
        Route::post('dictionaries', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'store'])->name('dictionaries.store');
        Route::get('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'show'])->name('dictionaries.show');
        Route::delete('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'destroy'])->name('dictionaries.destroy');

        Route::post('trainings', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'store'])->name('trainings.store');
        Route::post('trainings/{training}/start', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'start'])->name('trainings.start');
        Route::get('trainings/{training}/steps/next', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'next'])->name('training-steps.next');
        Route::get('trainings/{training}/steps/current', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'current'])->name('training-steps.next');
        Route::get('trainings/{training}/steps/{step}/progress', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'progress'])->name('trainings-steps.attempts');
        Route::post('trainings/{training}/steps/{step}/attempts', [\App\Http\Controllers\Api\V1\Training\TrainingStepAttemptController::class, 'store'])->name('trainings-steps.attempts');
        Route::get('trainings/{training}/steps/{step}/attempts', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'index'])->name('trainings-steps.attempts');
    });
});

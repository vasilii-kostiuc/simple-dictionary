<?php

use App\Http\Resources\ApiResponseResource;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use React\EventLoop\Loop;
use VasiliiKostiuc\LaravelMessagingLibrary\Messaging\MessageBrokerFactory;

// ОТКЛЮЧЕНО: Эти роуты блокируют PHP-FPM процессы
// Route::get('publish', function () {
//     Redis::publish('test-channel', json_encode([
//         'name' => 'Adam Wathan',
//     ]));
// });

// ОПАСНО! psubscribe блокирует процесс навсегда
// Route::get('subscribe', function () {
//     Redis::psubscribe(['*'], function (string $message, string $channel) {
//         echo $message;
//     });
// });


Route::prefix('v1')->group(function () {
    Route::post('send-to-wss', function (\Illuminate\Http\Request $request) {
        try {
            $data = $request->all();
            $channel = $data['channel'] ?? '';
            unset($data['channel']);

            $broker = app(MessageBrokerFactory::class)->create();
            // Используем нативный Redis Laravel вместо ReactPHP
            $broker->publish($channel, json_encode($data));

            //Loop::get()->stop();
            info("Published to channel: {$channel}", $data);
            return new ApiResponseResource(['success' => true]);
        } catch (\Exception $e) {
            info("Error publishing: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    });
    Route::post('auth/register', \App\Http\Controllers\Api\V1\Auth\RegisterController::class)->name('auth.register');
    Route::post('auth/login', \App\Http\Controllers\Api\V1\Auth\LoginController::class)->name('auth.login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile', [\App\Http\Controllers\Api\V1\Auth\ProfileController::class, 'update'])->name('profile.update');

        Route::post('auth/logout', \App\Http\Controllers\Api\V1\Auth\LogoutController::class)->name('auth.logout');
        Route::post('auth/token/validate', [\App\Http\Controllers\Api\V1\Auth\TokenController::class, 'validateToken'])->name('auth.token.validate');

        Route::get('languages', [\App\Http\Controllers\Api\V1\Language\LanguageController::class, 'index'])->name('languages.index');
        Route::get('languages/{language}', [\App\Http\Controllers\Api\V1\Language\LanguageController::class, 'show'])->name('languages.show');

        Route::get('dictionaries', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'index'])->name('dictionaries.index');
        Route::post('dictionaries', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'store'])->name('dictionaries.store');
        Route::get('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'show'])->name('dictionaries.show');
        Route::delete('dictionaries/{dictionary}', [\App\Http\Controllers\Api\V1\Dictionary\DictionaryController::class, 'destroy'])->name('dictionaries.destroy');

        Route::post('trainings', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'store'])->name('trainings.store');
        Route::get('trainings', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'index'])->name('trainings.index');
        Route::get('trainings/{training}', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'show'])->name('trainings.show');
        Route::post('trainings/{training}/start', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'start'])->name('trainings.start');
        Route::post('trainings/{training}/expire', [\App\Http\Controllers\Api\V1\Training\TrainingController::class, 'expire'])->name('trainings.expire');

        Route::get('trainings/{training}/steps/next', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'next'])->name('training-steps.next');
        Route::get('trainings/{training}/steps/current', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'current'])->name('training-steps.current');
        Route::get('trainings/{training}/steps/{step}', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'show'])->name('training-steps.show');
        Route::get('trainings/{training}/steps/{step}/progress', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'progress'])->name('trainings-steps.progress');
        Route::patch('trainings/{training}/steps/{step}/skip', [\App\Http\Controllers\Api\V1\Training\TrainingStepController::class, 'skip'])->name('trainings-steps.skip');
        Route::post('trainings/{training}/steps/{step}/attempts', [\App\Http\Controllers\Api\V1\Training\TrainingStepAttemptController::class, 'store'])->name('trainings-steps.attempts');
        Route::get('trainings/{training}/steps/{step}/attempts', [\App\Http\Controllers\Api\V1\Training\TrainingStepAttemptController::class, 'index'])->name('trainings-steps.attempts');
    });
});

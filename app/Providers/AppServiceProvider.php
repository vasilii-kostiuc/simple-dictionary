<?php

namespace App\Providers;

use App\Infrastructure\Uploads\ImageUploader;
use App\Infrastructure\Uploads\ImageUploaderInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use VasiliiKostiuc\PubSubBroker\Messaging\BrokerFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageUploaderInterface::class, ImageUploader::class);

        $this->app->singleton(BrokerFactory::class, function () {
            $driver = config('messaging.default');

            return new BrokerFactory($driver, config("messaging.{$driver}", []));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('match-links', function (Request $request) {
            return Limit::perMinute(10)->by((string) ($request->user()?->id ?? $request->ip()));
        });
    }
}

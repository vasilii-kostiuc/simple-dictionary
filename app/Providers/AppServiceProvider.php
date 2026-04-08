<?php

namespace App\Providers;

use App\Infrastructure\Uploads\ImageUploader;
use App\Infrastructure\Uploads\ImageUploaderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageUploaderInterface::class, ImageUploader::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

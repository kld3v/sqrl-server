<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ScanProcessingService;
use App\Services\shortURLService;
use App\Services\evaluateTrustService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton(shortURLService::class, function ($app) {
            return new shortURLService();
        });
        $this->app->singleton(evaluateTrustService::class, function ($app) {
            return new evaluateTrustService();
        });
        $this->app->singleton(ScanProcessingService::class, function ($app) {
            return new ScanProcessingService(
                $app->make(shortURLService::class),
                $app->make(evaluateTrustService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

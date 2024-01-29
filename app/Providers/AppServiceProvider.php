<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ScanProcessingService;
use App\Services\ShortURL\ShortURLMain;
use App\Services\EvaluateTrustService;
use App\Services\ShortUrl\ShortURLServices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton(ShortURLMain::class, function ($app) {
            return new ShortURLMain(
                $app->make(ShortURLServices::class)
            );
        });
        $this->app->singleton(EvaluateTrustService::class, function ($app) {
            return new EvaluateTrustService();
        });
        $this->app->singleton(ScanProcessingService::class, function ($app) {
            return new ScanProcessingService(
                $app->make(ShortURLMain::class),
                $app->make(EvaluateTrustService::class)
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

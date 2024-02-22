<?php

namespace App\Providers;

use App\Services\EvaluateTrustService;
use App\Services\ScanProcessingService;
use App\Services\ShortURL\ShortURLMain;
use Illuminate\Support\ServiceProvider;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ShortURL\ShortURLServices;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RemoveWww;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\UrlManipulations\RedirectionValue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        //Keep registrations from dev branch
        $this->app->singleton(ShortURLMain::class, function ($app) {
            return new ShortURLMain(
                $app->make(ShortURLServices::class)
            );
        });
        $this->app->singleton(EvaluateTrustService::class, function ($app) {
            return new EvaluateTrustService(
                $app->make(GoogleWebRisk::class),
                $app->make(VirusTotalService::class),
                $app->make(IpChecker::class),
                $app->make(RemoveWww::class),
                $app->make(RedirectionValue::class)
            );
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

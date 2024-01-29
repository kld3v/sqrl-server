<?php

namespace App\Providers;

use App\Services\GoogleWebRisk;
use App\Services\VirusTotalService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GoogleWebRisk::class, function ($app) {
            return new GoogleWebRisk();
        });
        $this->app->singleton(VirusTotalService::class, function ($app) {
            return new VirusTotalService();
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

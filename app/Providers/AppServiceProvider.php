<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Services\GoogleWebRisk;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

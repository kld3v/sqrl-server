<?php

namespace App\Providers;

use App\Services\ScanLayers\WhoIs;
use App\Services\ScanLayers\UrlHaus;
use App\Services\EvaluateTrustService;
use App\Services\ScanProcessingService;
use App\Services\ShortURL\ShortURLMain;
use App\Services\UrlManipulations\StringEntropy;
use Illuminate\Support\ServiceProvider;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\SubdomainEnum;
use App\Services\ScanLayers\BadDomainCheck;
use App\Services\ShortURL\ShortURLServices;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RemoveWww;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\UrlManipulations\HasSubdomain;
use App\Services\ScanLayers\LevenshteinAlgorithm;
use App\Services\UrlManipulations\RedirectionValue;
use App\Services\UrlManipulations\SubdomainExtract;

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
                $app->make(IpChecker::class),
                $app->make(RemoveWww::class),
                $app->make(RedirectionValue::class),
                $app->make(LevenshteinAlgorithm::class),
                $app->make(SubdomainExtract::class),
                $app->make(BadDomainCheck::class),
                $app->make(WhoIs::class),
                $app->make(HasSubdomain::class),
                $app->make(SubdomainEnum::class),
                $app->make(UrlHaus::class),
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CalculateVenues\TestAllGeoShit;

class TestVenueCalculations extends Command
{
    protected $signature = 'venue:calculate';
    protected $description = 'Process venue calculations and generate GeoJSON files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(TestAllGeoShit $testAllGeoShit)
    {
        $this->info('Starting venue calculations...');
        $testAllGeoShit->processAllUrlIds();
        $this->info('Venue calculations completed and GeoJSON files generated.');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CalculateVenues\CalculateVenueService;

class TestVenueCalculations extends Command
{
    protected $signature = 'venue:calculate';
    protected $description = 'Process venue calculations and generate GeoJSON files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(CalculateVenueService $calculateVenueService)
    {
        $this->info('Starting venue calculations...');
        $calculateVenueService->processAllUrlIds();
        $this->info('Venue calculations completed.');
    }
}

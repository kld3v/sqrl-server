<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\CalculateVenues\CalculateTriggerService;
use App\Services\CalculateVenues\ClusteringService;
use App\Services\CalculateVenues\ScanDataFormatService;
use App\Services\CalculateVenues\BorderCalculationService;

class CalculateTriggerService_Test extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_valid_urlId()
    {
        $scanDataFormatService = new ScanDataFormatService;
        $clusteringService = new ClusteringService;
        $borderCalculationService = new BorderCalculationService;

        $calculateTriggerService = new CalculateTriggerService($scanDataFormatService, $clusteringService, $borderCalculationService);
        
        // Act
        $result = $calculateTriggerService->checkAndTriggerClustering();

    }
}

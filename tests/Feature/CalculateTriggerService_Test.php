<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\CalculateVenues\CalculateTriggerService;
use App\Services\CalculateVenues\ClusteringService;
use App\Services\CalculateVenues\ScanDataFormatService;
use App\Services\CalculateVenues\BorderCalculationService;
use App\Services\CalculateVenues\BorderOptimisationService;

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
        $borderOptimisationService = new BorderOptimisationService;

        $calculateTriggerService = new CalculateTriggerService($scanDataFormatService, $clusteringService, $borderCalculationService, $borderOptimisationService);
        
        // Act
        $result = $calculateTriggerService->checkAndTriggerClustering();

    }
}

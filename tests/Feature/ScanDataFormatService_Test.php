<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\CalculateVenues\ScanDataFormatService;

class ScanDataFormatService_Test extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Given a valid $urlId, it should return a formatted array of latitude and longitude for all scans associated with the $urlId.
    public function test_valid_urlId()
    {
        // Arrange
        $urlId = 1;
        $service = new ScanDataFormatService();
        
        // Act
        $result = $service->formatScansForUrlId($urlId);
        // echo $result;
    }
}

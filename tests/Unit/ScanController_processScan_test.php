<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\URL;
use App\Http\Controllers\ScanController;
use Illuminate\Http\Request; 

class ScanController_processScan_test extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testProcessScanReturnsCorrectTrustScore()
    {
        // Arrange: Create a Request object with the necessary data
        $data = [
            'url' => 'https://find-and-update.company-information.service.gov.uk/company/14516665/officers',
            'user_id' => 1,
            'latitude' => '51.5074',
            'longitude' => '0.1278'
        ];
        $request = new Request($data);
    
        // Create an instance of ScanController
        $scanController = new ScanController();

        // Act: Call the processScan function with the Request object
        $response = $scanController->processScan($request);
    
        // Assert: Check if the response contains the expected trust score
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(50, $responseData['trust_score']);
    }
}

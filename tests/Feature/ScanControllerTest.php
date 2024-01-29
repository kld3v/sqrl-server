<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;  
use Illuminate\Foundation\Testing\WithFaker;        
use Tests\TestCase;                                 
use Illuminate\Http\Request;                        
use Illuminate\Validation\ValidationException;      
use App\Http\Controllers\ScanController;             
use App\Models\Scan;                                 
use Illuminate\Foundation\Testing\DatabaseMigrations; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\ScanProcessingService;
use App\Services\EvaluateTrustService;
use App\Services\ShortURL\ShortURLMain;
use App\Services\ShortUrl\ShortURLServices;

class ScanControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Creating a new Scan instance with valid data should return a JSON response with status code 201.
    public function test_create_new_scan_instance_with_valid_data()
    {
        $request = new Request([
            'url_id' => 1,
            'trust_score' => 5,
            'user_id' => 1,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);

        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);
        $response = $controller->store($request);


        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Retrieving a specific Scan instance with a valid ID should return a JSON response with the Scan data.
    public function test_retrieve_specific_scan_instance_with_valid_id()
    {
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);

        $response = $controller->show(1);

    
        $this->assertJson($response->getContent());
    }
    
    
        // Creating a new Scan instance with missing or invalid data should return a JSON response with a validation error message and status code 422.
    public function test_create_new_scan_instance_with_missing_or_invalid_data()
    {
        $request = new Request([
            'url_id' => 1,
            'trust_score' => 'invalid',
            'user_id' => 1,
            'latitude' => 'invalid',
            'longitude' => -122.4194,
        ]);
    
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);
        $response = $controller->store($request);
   
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Retrieving a specific Scan instance with an invalid ID should return a JSON response with a "Scan not found" message and status code 404.
    public function test_retrieve_specific_scan_instance_with_invalid_id()
    {
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);
        $response = $controller->show(154134);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    
        // Retrieving all Scan instances should return a JSON response with an array of all Scan data.
    public function test_retrieve_all_scan_instances()
    {
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);

        $response = $controller->index();
    
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
    
        // Deleting a specific Scan instance with a valid ID should remove the Scan from the database and return a JSON response with status code 204.
    public function test_delete_scan_instance_with_valid_id()
    {

        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);

        // Get the created scan ID
        $scanId = 2;
        

        // Delete the scan instance
        $response = $controller->destroy($scanId);
    
        // Assert the response status code is 204
        $this->assertEquals(204, $response->getStatusCode());
    
        $controller->show($scanId);
    }
    
        // Creating a new Scan instance with a URL ID that does not exist should return a JSON response with a "URL not found" message and status code 404.
    public function test_create_new_scan_instance_with_nonexistent_url_id()
    {
        $request = new Request([
            'url_id' => 32232423,
            'trust_score' => 5,
            'user_id' => 1,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);


        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);

        $response = $controller->store($request);

    
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        // $this->assertJsonStringEqualsJsonString('{"message": "URL not found"}', $response->getContent());
    }
    
    
        // Creating a new Scan instance with a User ID that does not exist should return a JSON response with a "User not found" message and status code 404.
    public function test_create_new_scan_instance_with_nonexistent_user_id()
    {
        $request = new Request([
            'url_id' => 1,
            'trust_score' => 5,
            'user_id' => 235354, // Nonexistent user ID
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);
    
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new evaluateTrustService();
        $scanProcessingService = new scanProcessingService($shortURLMain, $evaluateTrustService);

    
        $controller = new ScanController($scanProcessingService);

        $response = $controller->store($request);
    
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        // $this->assertJsonStringEqualsJson('{"message": "User not found"}', $response->getContent());
    }
    
}
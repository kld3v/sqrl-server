<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;  
use Illuminate\Foundation\Testing\WithFaker;        
use Tests\TestCase;                                 
use Illuminate\Http\Request;                        
use Illuminate\Validation\ValidationException;      
use App\Http\Controllers\URLController;             
use App\Models\URL;                                 
use Illuminate\Foundation\Testing\DatabaseMigrations; 



class URLControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_valid_url_and_trust_score()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 500
        ]);

        // echo var_export($request, true);

        $controller = new URLController();
        $response = $controller->store($request);
        
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getData()->url);
        $this->assertEquals(500, $response->getData()->trust_score);
    }
    
        // Existing URL instance ID provided, URL instance retrieved and returned as JSON
    // Existing URL instance ID provided, URL instance retrieved and returned as JSON
    public function test_existing_url_instance_id()
    {
        $url = URL::create([
            'url' => 'https://example.com',
            'trust_score' => 500,
        ]);
    
        $controller = new URLController();
        $response = $controller->show($url->id);
    
        $this->assertEquals($url->id, $response->getData()->id);
    }
    
        // Non-existing URL instance ID provided, 404 error returned
    public function test_non_existing_url_instance_id()
    {
        $controller = new URLController();
        $response = $controller->show(456346);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getData()->message);
    }
    
        // Empty request data provided, ValidationException thrown
    public function test_empty_request_data()
    {
        $request = new Request();
    
        $controller = new URLController();
    
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // Invalid URL provided, ValidationException thrown
    public function test_invalid_url()
    {
        $request = new Request();
        $request->replace([
            'url' => 'invalid_url',
            'trust_score' => 500
        ]);
    
        $controller = new URLController();
    
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // Invalid trust score provided, ValidationException thrown
    public function test_invalid_trust_score()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 'invalid_trust_score'
        ]);
    
        $controller = new URLController();
    
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // URL with length > 2048 provided, ValidationException thrown
    public function test_url_length_exceeds_limit()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com/' . str_repeat('a', 2049),
            'trust_score' => 500
        ]);
    
        $controller = new URLController();
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // Trust score < 0 provided, ValidationException thrown
    public function test_negative_trust_score()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => -1
        ]);
    
        $controller = new URLController();
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // Trust score > 1000 provided, ValidationException thrown
    public function test_invalid_trust_score_num()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 1500
        ]);
    
        $controller = new URLController();
        $this->expectException(ValidationException::class);
        $controller->store($request);
    }
    
        // Multiple URL instances created with different data, all instances retrieved and returned as JSON
    public function test_multiple_url_instances_created_and_retrieved()
    {
        // Create multiple URL instances with different data
        $request1 = new Request();
        $request1->replace([
            'url' => 'https://example1.com',
            'trust_score' => 500
        ]);
    
        $request2 = new Request();
        $request2->replace([
            'url' => 'https://example2.com',
            'trust_score' => 700
        ]);
    
        $controller = new URLController();
        $response1 = $controller->store($request1);
        $response2 = $controller->store($request2);
    
        // Retrieve the created URL instances
        $url1 = $controller->show($response1->getData()->id);
        $url2 = $controller->show($response2->getData()->id);
    
        // Assert the retrieved URL instances match the created data
        $this->assertEquals('https://example1.com', $url1->getData()->url);
        $this->assertEquals(500, $url1->getData()->trust_score);
    
        $this->assertEquals('https://example2.com', $url2->getData()->url);
        $this->assertEquals(700, $url2->getData()->trust_score);
    }
    
        // URL instance created with maximum length URL and maximum trust score, instance retrieved and returned as JSON
    public function test_valid_url_and_trust_score_max()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 1000
        ]);
    
        $controller = new URLController();
        $response = $controller->store($request);
    
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getData()->url);
        $this->assertEquals(1000, $response->getData()->trust_score);
    
        $urlId = $response->getData()->id;
        $showResponse = $controller->show($urlId);
    
        $this->assertEquals(200, $showResponse->getStatusCode());
        $this->assertEquals('https://example.com', $showResponse->getData()->url);
        $this->assertEquals(1000, $showResponse->getData()->trust_score);
    }
    
        // URL instance created with minimum length URL and minimum trust score, instance retrieved and returned as JSON
    public function test_valid_url_and_trust_scor_min()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 0
        ]);
    
        $controller = new URLController();
        $response = $controller->store($request);
    
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getData()->url);
        $this->assertEquals(0, $response->getData()->trust_score);
    
        $urlId = $response->getData()->id;
        $showResponse = $controller->show($urlId);
    
        $this->assertEquals(200, $showResponse->getStatusCode());
        $this->assertEquals('https://example.com', $showResponse->getData()->url);
        $this->assertEquals(0, $showResponse->getData()->trust_score);
    }
    
        // URL instance created with maximum length URL and minimum trust score, instance retrieved and returned as JSON
    public function test_valid_url_and_minimum_trust_score()
    {
        $request = new Request();
        $request->replace([
            'url' => 'https://example.com',
            'trust_score' => 0
        ]);
    
        $controller = new URLController();
        $response = $controller->store($request);
    
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getData()->url);
        $this->assertEquals(0, $response->getData()->trust_score);
    
        $urlId = $response->getData()->id;
        $showResponse = $controller->show($urlId);
    
        $this->assertEquals(200, $showResponse->getStatusCode());
        $this->assertEquals('https://example.com', $showResponse->getData()->url);
        $this->assertEquals(0, $showResponse->getData()->trust_score);
    }
    
}

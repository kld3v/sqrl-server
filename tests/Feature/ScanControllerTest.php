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
    
        $controller = new ScanController();
        $response = $controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Retrieving a specific Scan instance with a valid ID should return a JSON response with the Scan data.
    public function test_retrieve_specific_scan_instance_with_valid_id()
    {
        $controller = new ScanController();
        $response = $controller->show(1);

    
        $this->assertJson($response->getContent());
    }
    
        // Updating a specific Scan instance with a valid ID and valid data should return a JSON response with the updated Scan data.
    public function test_update_specific_scan_instance_with_valid_id_and_valid_data()
    {
        $request = new Request([
            'trust_score' => 8,
        ]);
    
        $controller = new ScanController();
        $response = $controller->update($request, 1);
    
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
    
        $controller = new ScanController();
        $response = $controller->store($request);
   
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Retrieving a specific Scan instance with an invalid ID should return a JSON response with a "Scan not found" message and status code 404.
    public function test_retrieve_specific_scan_instance_with_invalid_id()
    {
        $controller = new ScanController();
        $response = $controller->show(154134);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Updating a specific Scan instance with an invalid ID should return a JSON response with a "Scan not found" message and status code 404.
    public function test_update_specific_scan_instance_with_invalid_id()
    {
        $request = new Request([
            'trust_score' => 8,
        ]);
    
        $controller = new ScanController();
        $response = $controller->update($request, 2645564);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Updating a specific Scan instance with invalid data should return a JSON response with a validation error message and status code 422.
    public function test_update_scan_instance_with_invalid_data()
    {
        $request = new Request([
            'trust_score' => 'invalid',
        ]);
    
        $controller = new ScanController();
        $response = $controller->update($request, 1);


    
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('The given data was invalid.', $content['message']);

    }
    
        // Retrieving the URL associated with a Scan with an invalid ID should return a JSON response with a "Scan or URL not found" message and status code 404.
    public function test_retrieve_url_with_invalid_id()
    {
        $controller = new ScanController();
        $response = $controller->getUrl(1231121);
        
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        // Decode the JSON response content
        $actualJson = json_decode($response->getContent(), true);
        $expectedJson = json_decode('{"message": "Scan or URL not found"}', true);

        // Use PHPUnit's assertEquals to compare the expected and actual JSON arrays
        $this->assertEquals($expectedJson, $actualJson);
    }
    
        // Retrieving the User associated with a Scan with an invalid ID should return a JSON response with a "Scan or User not found" message and status code 404.
    public function test_retrieve_user_with_invalid_scan_id()
    {
        $controller = new ScanController();
        $response = $controller->getUser(3244323);

    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        // Decode the JSON response content
        $actualJson = json_decode($response->getContent(), true);
        $expectedJson = json_decode('{"message": "Scan or User not found"}', true);

        // Use PHPUnit's assertEquals to compare the expected and actual JSON arrays
        $this->assertEquals($expectedJson, $actualJson);
    }
    
        // Retrieving all Scan instances should return a JSON response with an array of all Scan data.
    public function test_retrieve_all_scan_instances()
    {
        $controller = new ScanController();
        $response = $controller->index();
    
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Retrieving the URL associated with a Scan with a valid ID should return a JSON response with the URL data.
    public function test_retrieve_url_associated_with_scan_with_valid_id()
    {

        $controller = new ScanController();

        // Retrieve the URL associated with the Scan
        $urlResponse = $controller->getUrl(1);

        $this->assertEquals(200, $urlResponse->getStatusCode());
        $this->assertJson($urlResponse->getContent());
    }

        // Retrieving the User associated with a Scan with a valid ID should return a JSON response with the User data.
    public function test_retrieve_user_with_valid_id()
    {
        $controller = new ScanController();

        // Retrieve the User associated with the Scan
        $response = $controller->getUser(1);
    
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
    
        // Deleting a specific Scan instance with a valid ID should remove the Scan from the database and return a JSON response with status code 204.
    public function test_delete_scan_instance_with_valid_id()
    {
        // Create a new Scan instance
        $request = new Request([
            'url_id' => 1,
            'trust_score' => 5,
            'user_id' => 1,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);
    
        $controller = new ScanController();
        $response = $controller->store($request);
    
        // Get the created scan ID
        $scanId = $response->json('id');
    
        // Delete the scan instance
        $response = $controller->destroy($scanId);
    
        // Assert the response status code is 204
        $this->assertEquals(204, $response->getStatusCode());
    
        // Assert the scan instance is deleted from the database
        $this->expectException(ModelNotFoundException::class);
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
    
        $controller = new ScanController();
        $response = $controller->store($request);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJsonString('{"message": "URL not found"}', $response->getContent());
    }
    
        // Retrieving a specific Scan instance with a deleted ID should return a JSON response with a "Scan not found" message and status code 404.
    public function test_retrieve_deleted_scan_instance()
    {
        $controller = new ScanController();
        $response = $controller->show(999);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJsonString('{"message": "Scan not found"}', $response->getContent());
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
    
        $controller = new ScanController();
        $response = $controller->store($request);
    
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJson('{"message": "User not found"}', $response->getContent());
    }
    
        // Retrieving the URL associated with a deleted Scan should return a JSON response with a "Scan or URL not found" message and status code 404.
    public function test_retrieve_url_associated_with_deleted_scan()
    {
        // Create a new Scan instance
        $request = new Request([
            'url_id' => 1,
            'trust_score' => 5,
            'user_id' => 1,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);
    
        $controller = new ScanController();
        $response = $controller->store($request);
    
        // Delete the Scan instance
        $scanId = $response->json('id');
        Scan::destroy($scanId);
    
        // Retrieve the URL associated with the deleted Scan
        $response = $controller->getUrl($scanId);
    
        // Assert the response
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals('Scan or URL not found', $response->json('message'));
    }
    
}

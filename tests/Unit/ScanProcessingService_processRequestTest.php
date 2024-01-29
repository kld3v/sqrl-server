<?php

namespace Tests\Feature;

use App\Http\Controllers\URLController;
use App\Services\EvaluateTrustService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ScanProcessingService;
use App\Services\ShortURL\ShortURLMain;
use App\Http\Controllers\ScanController;
use App\Models\URL;
use App\Services\ShortUrl\ShortURLServices;




class ScanProcessingService_processRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Evaluates the trust score of a new URL, stores it in the database, and returns its ID and trust score
    // Adding a new URL to the database returns the URL ID and trust score
    // processRequest with valid URL returns url_id and trust_score
    public function test_process_request_with_valid_url()
    {   
        $shortURLService = new ShortURLServices();
        $shortURLMain = new ShortURLMain($shortURLService);
        $evaluateTrustService = new EvaluateTrustService();
        $scanProcessingService = new ScanProcessingService($shortURLMain, $evaluateTrustService);
    
        $url = "https://laravel.com/docs/10.x/ergageraergrg";
        $result = $scanProcessingService->processRequest($url);
        $this->assertNotNull($result);
    }


        // processRequest with existing URL updates trust_score and returns url_id and trust_score
    // public function test_process_request_with_existing_url()
    // {
    //     $shortURLMain = new ShortURLMain();
    //     $evaluateTrustService = new evaluateTrustService();
    //     $scanProcessingService = new ScanProcessingService($shortURLMain, $evaluateTrustService);

    //     // Create a mock URL record
    //     $existingUrl = new URL();
    //     $existingUrl->url = "https://laravel.com/docs/10.x/logging";
    //     $existingUrl->trust_score = 5;

    //     // Mock the URL model's where() method to return the mock URL record
    //     URL::shouldReceive('where')->with('url', 'https://laravel.com/docs/10.x/logging')->andReturn($existingUrl);

    //     // Mock the evaluateTrust() method to return a different trust score
    //     // $evaluateTrustService->shouldReceive('evaluateTrust')->andReturn(50);

    //     $url = "https://laravel.com/docs/10.x/logging";
    //     $result = $scanProcessingService->processRequest($url);

    //     echo $result;

    //     $this->assertEquals($result->trust_score, 50);
    //     $this->assertNotNull($result->id);
    // }
}   

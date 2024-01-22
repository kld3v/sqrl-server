<?php

namespace Tests\Feature;

use App\Http\Controllers\URLController;
use App\Services\evaluateTrustService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ScanProcessingService;
use App\Services\shortURLService;



class ScanProcessingService_processRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Evaluates the trust score of a new URL, stores it in the database, and returns its ID and trust score
    public function test_evaluate_new_url()
    {
        // Arrange
        $url = 'https://en.wikipedia.org/wiki/Chief_product_officer';
        $trustScore = 80;
        $existingUrl = null;
        
        $shortUrlServiceMock = $this->createMock(shortURLService::class);
        $shortUrlServiceMock->expects($this->once())
            ->method('isShortURL')
            ->with($url)
            ->willReturn(false);
        
        $urlControllerMock = $this->createMock(URLController::class);
        $urlControllerMock->expects($this->once())
            ->method('findUrlByString')
            ->with($url)
            ->willReturn($existingUrl);
        $urlControllerMock->expects($this->once())
            ->method('store')
            ->willReturn($existingUrl);
        
        $evaluateTrustServiceMock = $this->createMock(evaluateTrustService::class);
        $evaluateTrustServiceMock->expects($this->once())
            ->method('evaluateTrust')
            ->with($url)
            ->willReturn($trustScore);
        
        $scanProcessingService = new ScanProcessingService($shortUrlServiceMock, $evaluateTrustServiceMock);
        $expectedResult = [
            'url_id' => null,
            'trust_score' => $trustScore,
        ];
        
        // Act
        $result = $scanProcessingService->processRequest($url);
        
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}

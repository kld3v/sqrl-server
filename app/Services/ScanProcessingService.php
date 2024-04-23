<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use App\Services\ShortURL\Resolvers\HeadlessBrowser;
use Illuminate\Support\Facades\App;
use App\Services\EvaluateTrustService;
use App\Services\ShortURL\ShortURLMain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\URL;
use App\Services\UrlManipulations\RedirectionValue;
use Illuminate\Support\Facades\Log;

class ScanProcessingService
{
    protected $shortURLMain;
    protected $evaluateTrustService;

    //DARYA REMEMBER TO ALWAYS UPDATE THIS CHEERS LOVE JOEL
    protected $currentTestVersion = '1.0.5';


    public function __construct(ShortURLMain $shortURLMain, EvaluateTrustService $evaluateTrustService)
    {
        $this->shortURLMain = $shortURLMain;
        $this->evaluateTrustService = $evaluateTrustService;
    }
    public function processScan($url)
    {
        $startTime = microtime(true);
    
        Log::channel('performanceLog')->info("Starting process scan with URL: {$url}");
    
        // Expanding shortened URL, if necessary
        $redirectionValue = new RedirectionValue();
        $headlessBrowser = new HeadlessBrowser();
    
        if ($redirectionValue->redirectionValue($url)) {
            $browserStartTime = microtime(true);
            $url = $headlessBrowser->interactWithPage($url);
            $browserEndTime = microtime(true);
            Log::channel('performanceLog')->info("Headless browser interaction took: " . ($browserEndTime - $browserStartTime) . " seconds");
        }
    
        // Check if URL is already in the database
        $dbLookupStartTime = microtime(true);
        $existingUrl = URL::where('url', $url)->first();
        $dbLookupEndTime = microtime(true);
        Log::channel('performanceLog')->info("Database lookup took: " . ($dbLookupEndTime - $dbLookupStartTime) . " seconds");
    
        if ($existingUrl) {
            // Check if the trust score needs to be updated
            $updateStartTime = microtime(true);
            if ($this->isTrustScoreOutdated($existingUrl)) {
                $trustScore = $this->evaluateTrustService->evaluateTrust($url);
                $score = $trustScore['trust_score'];
    
                $existingUrl->update([
                    'trust_score' => $score,
                    'test_version' => $this->currentTestVersion,
                ]);
            } else {
                $trustScore = $existingUrl->trust_score;
            }
            $updateEndTime = microtime(true);
            Log::channel('performanceLog')->info("Update process took: " . ($updateEndTime - $updateStartTime) . " seconds");
        } else {
            // URL not in DB, evaluate and add
            $evaluateStartTime = microtime(true);
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);
    
            $score = $trustScore['trust_score'];
            $existingUrl = URL::create([
                'url' => $url,
                'trust_score' => $score,
                'test_version' => $this->currentTestVersion,
            ]);
            $evaluateEndTime = microtime(true);
            Log::channel('performanceLog')->info("Evaluation and creation took: " . ($evaluateEndTime - $evaluateStartTime) . " seconds");
        }
    
        $endTime = microtime(true);
        Log::channel('performanceLog')->info("Total process scan time: " . ($endTime - $startTime) . " seconds");
    
        return $existingUrl;
    }    

    private function isTrustScoreOutdated($urlRecord)
    {
        // Check if the updated_at column is older than 2 weeks or if test version has changed
        $isDateOutdated = $urlRecord->updated_at->lt(Carbon::now()->subWeeks(2));
        $isVersionOutdated = $urlRecord->test_version !== $this->currentTestVersion;
        
        return $isDateOutdated || $isVersionOutdated;
    }

    public function testProcessScan($url)
    {
        // Start time for URL processing
        $startTime = microtime(true);
    
        $initialUrl = $url; 
        $redirectionOccurred = false; 
    
        // Expanding shortened URL, if necessary
        $redirectionValue = new RedirectionValue();
        $headlessBrowser = new HeadlessBrowser();
        if ($redirectionValue->redirectionValue($url)) {
            $redirectionOccurred = true; // Redirection has occurred
            $url = $headlessBrowser->interactWithPage($url);
        }
    
        // End time for URL processing
        $urlProcessEndTime = microtime(true);
    
        // Evaluate trust score
        $trustScoreProcessStartTime = microtime(true);
        $trustScore = $this->evaluateTrustService->evaluateTrust($url);
        $trustScoreProcessEndTime = microtime(true);
    
        // Calculate durations
        $urlProcessDuration = $urlProcessEndTime - $startTime;
        $trustScoreProcessDuration = $trustScoreProcessEndTime - $trustScoreProcessStartTime;
        $totalProcessDuration = $trustScoreProcessEndTime - $startTime;
    
        return [
            'initial_url' => $initialUrl,
            'final_url' => $url,
            'redirection' => $redirectionOccurred,
            'trust_score' => $trustScore,
            'test_version' => $this->currentTestVersion,
            'url_process_time' => $urlProcessDuration,
            'trust_score_process_time' => $trustScoreProcessDuration,
            'total_process_time' => $totalProcessDuration,
        ];
    }
    
}

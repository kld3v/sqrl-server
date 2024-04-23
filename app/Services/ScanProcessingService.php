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
            $expandStart = microtime(true);
            $url = $headlessBrowser->interactWithPage($url);
            Log::channel('performanceLog')->info("URL expansion took: " . (microtime(true) - $expandStart) . " seconds");
        }
    
        // Check if URL is already in the database
        $dbLookupStart = microtime(true);
        $existingUrl = URL::where('url', $url)->first();
        Log::channel('performanceLog')->info("Database lookup took: " . (microtime(true) - $dbLookupStart) . " seconds");
    
        if ($existingUrl) {
            // Check if the trust score needs to be updated
            if ($this->isTrustScoreOutdated($existingUrl)) {
                $trustScoreStart = microtime(true);
                $trustScore = $this->evaluateTrustService->evaluateTrust($url);
                $score = $trustScore['trust_score'];
                $existingUrl->update([
                    'trust_score' => $score,
                    'test_version' => $this->currentTestVersion,
                ]);
                Log::channel('performanceLog')->info("Trust score update took: " . (microtime(true) - trustScoreStart) . " seconds");
            } else {
                $trustScore = $existingUrl->trust_score;
            }
        } else {
            // URL not in DB, evaluate and add
            $evaluateStart = microtime(true);
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);
            $score = $trustScore['trust_score'];
            $existingUrl = URL::create([
                'url' => $url,
                'trust_score' => $score,
                'test_version' => $this->currentTestVersion,
            ]);
            Log::channel('performanceLog')->info("Trust score evaluation and DB insertion took: " . (microtime(true) - evaluateStart) . " seconds");
        }
    
        Log::channel('performanceLog')->info("Total process time: " . (microtime(true) - startTime) . " seconds");
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

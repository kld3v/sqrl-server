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
    protected $currentTestVersion = '1.0.6';


    public function __construct(ShortURLMain $shortURLMain, EvaluateTrustService $evaluateTrustService)
    {
        $this->shortURLMain = $shortURLMain;
        $this->evaluateTrustService = $evaluateTrustService;
    }
    public function checkUrl($url)
    {
        Log::channel('redirectLog')->info("Starting process scan with URL: {$url}");
        // Expanding shortened URL, if necessary
        $redirectionValue = new RedirectionValue();
        $headlessBrowser = new HeadlessBrowser();

        if ($redirectionValue->redirectionValue($url)) {
            $url = $headlessBrowser->interactWithPage($url);
        }
        // Check if URL is already in the database
        $existingUrl = URL::where('url', $url)->first();

        if ($existingUrl) {
            // Check if the trust score needs to be updated
            if ($this->isTrustScoreOutdated($existingUrl)) {

                $trustScore = $this->evaluateTrustService->evaluateTrust($url);
                $score = $trustScore['trust_score'];

                $existingUrl->update(
                    [
                        'trust_score' => $score,
                        'test_version' => $this->currentTestVersion,
                    ]
                );

                $existingUrl->touch();
                
            } else {
                $trustScore = $existingUrl->trust_score;
            }
        } else {
            // URL not in DB, evaluate and add
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);
            $score = $trustScore['trust_score'];
            $existingUrl = URL::create([
                'url' => $url,
                'trust_score' => $score,
                'test_version' => $this->currentTestVersion,
            ]);
        }
        return $existingUrl;
    }
    
    private function isTrustScoreOutdated($urlRecord)
    {
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

<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;
use App\Services\evaluateTrustService;
use App\Services\shortURLService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\URL;

use Illuminate\Support\Facades\Log;

class ScanProcessingService
{
    protected $shortUrlService;
    protected $evaluateTrustService;

    public function __construct(shortURLService $shortUrlService, evaluateTrustService $evaluateTrustService)
    {
        $this->shortUrlService = $shortUrlService;
        $this->evaluateTrustService = $evaluateTrustService;
    }
    public function processRequest($url)
    {   
        // Expanding shortened URL, if necessary
        if ($this->shortUrlService->isShortURL($url)) {
            $url = $this->shortUrlService->expandURL($url);
        }

        // Check if URL is already in the database
        $existingUrl = URL::where('url', $url)->first();

        if ($existingUrl) {
            // Check if the trust score needs to be updated
            if ($this->isTrustScoreOutdated($existingUrl)) {
                $existingUrl->update([
                    'trust_score' => $this->evaluateTrustService->evaluateTrust($url)
                ]);
            } else {
                $trustScore = $existingUrl->trust_score;
            }
        } else {
            // URL not in DB, evaluate and add
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);
            $existingUrl = URL::create(['url' => $url, 'trust_score' => $trustScore]);
        }

        return $existingUrl;
    }

    private function isTrustScoreOutdated($urlRecord)
    {
        // Check if the updated_at column is older than 2 weeks
        return $urlRecord->updated_at->lt(Carbon::now()->subWeeks(2));
    }

}

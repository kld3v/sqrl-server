<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;
use App\Services\evaluateTrustService;
use App\Services\shortURLService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class ScanProcessingService
{
    protected $urlController;
    protected $scanController;
    protected $shortUrlService;
    protected $evaluateTrustService;

    public function __construct(shortURLService $shortUrlService, evaluateTrustService $evaluateTrustService)
    {
        $this->urlController = App::make(URLController::class);
        $this->scanController = App::make(ScanController::class);
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
        $existingUrl = $this->urlController->findUrlByString($url);

        if ($existingUrl) {
            // Check if the trust score needs to be updated
            if ($this->isTrustScoreOutdated($existingUrl)) {
                $trustScore = $this->evaluateTrustService->evaluateTrust($url);
                $this->urlController->updateTrustScore($existingUrl, $trustScore);

            } else {
                $trustScore = $existingUrl->trust_score;
            }
        } else {
            // URL not in DB, evaluate and add
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);


            $urlData = ['URL' => $url, 'trust_score' => $trustScore];
            $existingUrl = $this->urlController->store(new Request($urlData));

        }


        return [
            'url_id' => $existingUrl->id,
            'trust_score' => $trustScore,
        ];
    }

    private function isTrustScoreOutdated($urlRecord)
    {
        // Check if the updated_at column is older than 2 weeks
        return $urlRecord->updated_at->lt(Carbon::now()->subWeeks(2));
    }

}

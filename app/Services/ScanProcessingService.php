<?php


namespace App\Services;

use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\App;
use App\Services\EvaluateTrustService;
use App\Services\ShortURL\ShortURLMain;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\URL;

use Illuminate\Support\Facades\Log;

class ScanProcessingService
{
    protected $shortURLMain;
    protected $evaluateTrustService;

    public function __construct(ShortURLMain $shortURLMain, EvaluateTrustService $evaluateTrustService)
    {
        $this->shortURLMain = $shortURLMain;
        $this->evaluateTrustService = $evaluateTrustService;
    }
    public function processScan($url)
    {   
        
        // Expanding shortened URL, if necessary
        if ($this->shortURLMain->isShortURL($url)) {
            $url = $this->shortURLMain->unshorten($url);
        }

        // Check if URL is already in the database
        $existingUrl = URL::where('url', $url)->first();
        

        if ($existingUrl) {
            // Check if the trust score needs to be updated
            if ($this->isTrustScoreOutdated($existingUrl)) {
                $existingUrl->update([
                    'trust_score' => $this->evaluateTrustService->evaluateTrust($url)
                    
                ]
            );
        
            } else {

                $trustScore = $existingUrl->trust_score;
            }
        } else {
            
            // URL not in DB, evaluate and add
            $trustScore = $this->evaluateTrustService->evaluateTrust($url);
           
            $score = $trustScore['trust_score'];         
            
            $existingUrl = URL::create(['url' => $url, 'trust_score' => $score]);
        }

        return $trustScore;
    }

    private function isTrustScoreOutdated($urlRecord)
    {
        // Check if the updated_at column is older than 2 weeks
        return $urlRecord->updated_at->lt(Carbon::now()->subWeeks(2));
    }

}

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

    //MAJID REMEMBER TO ALWAYS UPDATE THIS CHEERS LOVE JOEL
    protected $currentTestVersion = '1.0.3';


    public function __construct(ShortURLMain $shortURLMain, EvaluateTrustService $evaluateTrustService)
    {
        $this->shortURLMain = $shortURLMain;
        $this->evaluateTrustService = $evaluateTrustService;
    }
    public function processScan($url)
    {
        Log::channel('redirectLog')->info("Starting process scan with URL: {$url}");
        // Expanding shortened URL, if necessary
        $redirectionValue = new RedirectionValue();
        $headlessBrowser = new HeadlessBrowser();
        if ($redirectionValue->redirectionValue($url)) {
            var_dump('true');
            // var_dump('entered url' . $url);
            //$url = $this->shortURLMain->unshorten($url);
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
        // Check if the updated_at column is older than 2 weeks or if test version has changed significantly
        $isDateOutdated = $urlRecord->updated_at->lt(Carbon::now()->subWeeks(2));
        $isVersionOutdated = $this->isVersionChangeSignificant($urlRecord->test_version, $this->currentTestVersion);

        return $isDateOutdated || $isVersionOutdated;
    }

    private function isVersionChangeSignificant($storedVersion, $currentVersion)
    {

        return $storedVersion !== $currentVersion;
    }
}

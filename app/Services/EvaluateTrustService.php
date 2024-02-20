<?php


namespace App\Services;

use App\Services\UrlResolver\UrlRedirectionChecker;
use App\Services\UrlResolver\UrlResolver;
use App\Services\UrlResolver\BadDomainChecker;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\ScanLayers\UrlCleaner\UrlCleaner;


class EvaluateTrustService
{
    protected $webRiskService;
    protected $virusTotalService;
    public function __construct(GoogleWebRisk $webRiskService, VirusTotalService $virusTotalService)
    {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
    }
    private function isIpAddress($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
    public function evaluateTrust($url)
    {
        $urlCleaner = new UrlCleaner();
        $modifiedUrl = $urlCleaner->removeWWW($url);
        //redirections tracking:
        $redirectionChecker = new UrlRedirectionChecker($modifiedUrl);
        $hasRedirection = $redirectionChecker->checkRedirection();
        if ($hasRedirection) {
            $command = base_path('app/Scripts/RedirectTracker.sh') . ' ' . escapeshellarg($url);
            $output = shell_exec($command);
            $parsed=parse_url($output,PHP_URL_SCHEME);
            if ($parsed === 'http'){
                return [
                    'trust_score'=>0,
                    'reason'=>'http in the last redirection'
                ];
            }
        }


        $urlResolver = new UrlResolver();
        $resolvedUrl = $urlResolver->resolveUrl($modifiedUrl);
        
        //only works when there are no subdomains
        if ($this->isIpAddress($resolvedUrl['domain'])) {
            return [
                'trust_score' => 0,
                'reason' => 'url is in an IP form'
            ];
        }
        return 's';
        $badDomainChecker = new BadDomainChecker();
        if ($badDomainChecker->checkDomain($modifiedUrl)) {
            return [
                'trust_score' => 0,
                'reason' => 'URL is in the bad domains list',
            ];
        } 

        return 1;

        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($url);
        if (parse_url($url, PHP_URL_SCHEME) === 'http') {
            return [
                'trust_score' => 0,
                'reason' => 'This uri is a http protocol (not secured)',
            ];
        }

        $output = shell_exec($command);
        //return $output;
        //$cleanedOutput = preg_replace('/[[:cntrl:]]/', '', $output);
        $sslCheckResult = json_decode($output, true);

        return $sslCheckResult;
        if (isset($sslCheckResult['error'])) {
            return [
                'trust_score' => 0,
                'reason' => $sslCheckResult,
            ];
        }
        if ($sslCheckResult['trust_status'] !== 'URL is considered trustworthy based on the public key.') {
            return ['trust_score' => 0];
        }

        //google
        $googleWebRiskResult = $this->webRiskService->checkForThreats($url);
        if ($googleWebRiskResult['threat_detected']) {
            return [
                'trust_score' => 0,
            ];
        }

        // Use VirusTotal
        $virusTotalResult = $this->virusTotalService->checkMaliciousUrl($url);
        if ($virusTotalResult !== null) {
            $maliciousCount = $virusTotalResult['virus_total_malicious_count'];
            return [
                'trust_score' => 0,
                'virus_total_malicious_count' => $maliciousCount
            ];
        }
        return [
            'trust_score' => 1000,
        ];
    }

}

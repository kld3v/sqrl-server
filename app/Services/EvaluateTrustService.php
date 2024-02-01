<?php


namespace App\Services;

use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\VirusTotalService;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;


class EvaluateTrustService
{
    protected $webRiskService;
    protected $virusTotalService;
    public function __construct(GoogleWebRisk $webRiskService, VirusTotalService $virusTotalService)
    {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
    }
    public function evaluateTrust($url)
    {

        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($url);
        if (parse_url($url, PHP_URL_SCHEME) === 'http') {
            return [
                'trust_score' => 0,
                'reason' => 'This uri is a http protocol (not secured)',
            ];
        }
        $output = shell_exec($command);

        $cleanedOutput = preg_replace('/[[:cntrl:]]/', '', $output);
        //return gettype($cleanedOutput);
        $sslCheckResult = json_decode($cleanedOutput, true);
        //return $sslCheckResult;
        if (isset($sslCheckResult['error'])) {
            return [
                'trust_score' => 0,
                'reason' => $sslCheckResult,
            ];
        }
        if ($sslCheckResult['trust_status'] !== 'URL is considered trustworthy based on the public key.') {
            return ['trust_score' => 0];
        }

        // Continue logic for Google Web Risk
        $googleWebRiskResult = $this->webRiskService->checkForThreats($url);

        if ($googleWebRiskResult['threat_detected']) {
            return [
                'trust_score' => 0,
            ];
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

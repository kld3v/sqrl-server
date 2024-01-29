<?php


namespace App\Services;

use App\Services\GoogleWebRisk;
use App\Services\VirusTotalService;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;


class EvaluateTrustService {
    protected $webRiskService;
    protected $virusTotalService;
    public function __construct(GoogleWebRisk $webRiskService, VirusTotalService $virusTotalService)
    {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
    }
    public function evaluateTrust($url) {
        
        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($url);
        $output = shell_exec($command);
        dd($output);
        $sslCheckResult = json_decode($output, true);
        //dd($sslCheckResult);
        if (isset($sslCheckResult['error']) || $sslCheckResult['trust_status'] !== "URL is considered trustworthy based on the public key.") {
            return response()->json([
                'trust_score' => 0,
                'reason'=> $sslCheckResult,
            ]);
        }
        //google
        $googleWebRiskResult = $this->webRiskService->checkForThreats($url);

        if ($googleWebRiskResult['threat_detected']) {
            return response()->json([
                'trust_score' => 0,
            ]);
        }

        // Use VirusTotal
        $virusTotalResult = $this->virusTotalService->checkMaliciousUrl($url);

        if ($virusTotalResult !== null) {
            $maliciousCount = $virusTotalResult['virus_total_malicious_count'];
            return response()->json([
                'trust_score' => 0,
                'virus_total_malicious_count'=>$maliciousCount
            ]);
        }
        // If the URL is not detected as a threat by either Google Web Risk or VirusTotal, return a security score of 1000
        return response()->json([
            'trust_score' => 1000,
        ]);
    }
    
}
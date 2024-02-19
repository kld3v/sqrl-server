<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleWebRisk;
use App\Services\VirusTotalService;

class RiskEvaluationController extends Controller
{
    protected $webRiskService;
    protected $virusTotalService;

    public function __construct(GoogleWebRisk $webRiskService, VirusTotalService $virusTotalService)
    {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
    }

    public function checkWebRisk(Request $request)
    {
        $uri = 'google.com';
        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($uri);
        $output = shell_exec($command);
        //dd($output);
        $sslCheckResult = json_decode($output, true);
        //dd($sslCheckResult);
        if (isset($sslCheckResult['error']) || $sslCheckResult['trust_status'] !== "URL is considered trustworthy based on the public key.") {
            return response()->json([
                'security_score' => 0,
                'reason'=> $sslCheckResult,
            ]);
        }
        //google
        $googleWebRiskResult = $this->webRiskService->checkForThreats($uri);

        if ($googleWebRiskResult['threat_detected']) {
            return response()->json([
                'security_score' => 0,
            ]);
        }

        // Use VirusTotal
        $virusTotalResult = $this->virusTotalService->checkMaliciousUrl($uri);

        if ($virusTotalResult !== null) {
            $maliciousCount = $virusTotalResult['virus_total_malicious_count'];
            return response()->json([
                'security_score' => 0,
                'virus_total_malicious_count'=>$maliciousCount
            ]);
        }

        // If the URL is not detected as a threat by either Google Web Risk or VirusTotal, return a security score of 1000
        return response()->json([
            'security_score' => 1000,
        ]);
    }
}

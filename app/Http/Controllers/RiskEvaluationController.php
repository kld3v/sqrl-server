<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\Services\VirusTotalService;
use App\Providers\Services\GoogleWebRisk;

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
        //check the url here
        $uri = 'http://125.44.173.50:53386/i';
        $googleWebRiskResult = $this->webRiskService->checkForThreats($uri);
        if ($googleWebRiskResult['threat_detected']) {
            return response()->json([
                'security_score' => 0,
                'virus_total_malicious_count' => 0
            ]);
        }
        //checking againts virus total
        $virusTotalResult = $this->virusTotalService->checkMaliciousUrl($uri);
        if ($virusTotalResult !== null) {
            return response()->json($virusTotalResult);
        }
        return response()->json([
            'security_score' => 1000,
            'virus_total_malicious_count' => 0
        ]);
    }
}

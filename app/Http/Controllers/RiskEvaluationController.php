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
        $uri = 'http://115.53.232.114:56123/i';
        $apiKeyWebRisk = env('WEB_RISK_API_KEY');

        // Check with Google Web Risk service
        $googleWebRiskResult = $this->webRiskService->checkWebRisk($uri, $apiKeyWebRisk);
        $threatTypesToCheck = ['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING', 'SOCIAL_ENGINEERING_EXTENDED_COVERAGE'];

        $threatDetected = false;

        foreach ($threatTypesToCheck as $threatType) {
            if (
                isset($googleWebRiskResult[$threatType]['body']['threat']['threatTypes'])
                && in_array($threatType, $googleWebRiskResult[$threatType]['body']['threat']['threatTypes'])
            ) {
                $threatDetected = true;
                break;
            }
        }

        if (!$threatDetected) {
            // If no threat is detected by Google Web Risk, use VirusTotalService for additional check
            $virusTotalResult = $this->virusTotalService->scanUrl($uri);
            $analysisId = $virusTotalResult['data']['id'];
            $analysisDetails = $this->virusTotalService->getAnalysisDetails($analysisId);

            if ($analysisDetails['data']['attributes']['stats']['malicious'] > 0) {
                return response()->json([
                    'security_score' => 0,
                    'virus_total_malicious_count' => $analysisDetails['data']['attributes']['stats']['malicious'],
                    //'virus_total_result' => $virusTotalResult,
                ]);
            }
        }

        // If neither Google Web Risk nor VirusTotal indicates a threat, return 'security_score' as 500
        return response()->json([
            'security_score' => 1000,
            'virus_total_malicious_count' => 0
        ]);
    }
}

<?php

namespace App\Http\Controllers;
use App\Providers\Services\GoogleWebRisk;
use Illuminate\Http\Request;

class RiskEvaluationController extends Controller
{
    protected $webRiskService;

    public function __construct(GoogleWebRisk $webRiskService)
    {
        $this->webRiskService = $webRiskService;
    }

    public function checkWebRisk(Request $request)
    {
        $uri = 'discordsteams.com'; 
        $apiKey = env('WEB_RISK_API_KEY');

        $googleWebRiskResult = $this->webRiskService->checkWebRisk($uri, $apiKey);
        $threatTypesToCheck = ['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING','SOCIAL_ENGINEERING_EXTENDED_COVERAGE'];

        foreach ($threatTypesToCheck as $threatType) {
            if (
                isset($googleWebRiskResult[$threatType]['body']['threat']['threatTypes'])
                && in_array($threatType, $googleWebRiskResult[$threatType]['body']['threat']['threatTypes'])
            ) {
               
                return response()->json(['security_score' => 0]);
            }
        }
    
        return response()->json(['security_score' => 500]);
    }

}

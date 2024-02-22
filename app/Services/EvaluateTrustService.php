<?php


namespace App\Services;

use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RedirectionValue;
use App\Services\UrlManipulations\RemoveWww;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;


class EvaluateTrustService
{
    protected $webRiskService;
    protected $virusTotalService;

    protected $ipChecker;

    protected $redirectionValue;
    public function __construct(
        GoogleWebRisk $webRiskService,
        VirusTotalService $virusTotalService,
        IpChecker $ipChecker,
        RemoveWww $removeWWW,
        RedirectionValue $redirectionValue
    ) {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
        $this->ipChecker = $ipChecker;
        $this->removeWWW = $removeWWW;
        $this->redirectionValue = $redirectionValue;
    }
    public function evaluateTrust($url)
    {
        //removing www from all ulrs
        $modifiedUrl = $this->removeWWW->removeWWW($url);
        //return $modifiedUrl;
        //handeling urls with IP address
        if ($this->ipChecker->isIpAddress($modifiedUrl)) {
            return [
                'trust_score' => 0,
                'reason' => 'IP address detected. Only domain names are allowed.'
            ];
        }
        //redirection and http cases:
        if ($this->redirectionValue->redirectionValue($modifiedUrl)) {
            $redirectionChecker = base_path('app/Scripts/RedirectionCheck.sh') . ' ' . escapeshellarg($modifiedUrl);
            $redirectionTracker = shell_exec($redirectionChecker);
            $rediItem = json_decode($redirectionTracker)->fd;
            if (parse_url($rediItem, PHP_URL_SCHEME) !== 'https') {
                return [
                    'trust_score' => 0,
                    'reason' => 'url Schme in redirection is not https'
                ];
            }
        } elseif (
            parse_url($modifiedUrl, PHP_URL_SCHEME) == 'http'
        ) {
            return [
                'trust_score' => 0,
                'reason' => 'url Schme has no redirection and is http'
            ];
        }
        //handeling similarities to famouse domain names:
        

        // $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($url);
        //  if (parse_url($url, PHP_URL_SCHEME) === 'http') {
        //     return [
        //       'trust_score' => 0,
        //       'reason' => 'This uri is a http protocol (not secured)',
        //    ];
        //  }
        //$output = shell_exec($command);
        //return $output;
        //$cleanedOutput = preg_replace('/[[:cntrl:]]/', '', $output);
        //$sslCheckResult = json_decode($output, true);
        //return $sslCheckResult;
        // if (isset($sslCheckResult['error'])) {
        //     return [
        //        'trust_score' => 0,
        //      'reason' => $sslCheckResult,
        //   ];
        // }
        // if ($sslCheckResult['trust_status'] !== 'URL is considered trustworthy based on the public key.') {
        //     return ['trust_score' => 0];
        //  }

        //google
        // $googleWebRiskResult = $this->webRiskService->checkForThreats($url);
        // //var_dump($googleWebRiskResult);
        // foreach (['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING'] as $threatType) {
        //     if (isset($googleWebRiskResult[$threatType]['threat']) && $googleWebRiskResult[$threatType]['threat'] !== null) {
        //         return [
        //             'trust_score' => 0,
        //             'reason' => 'Google Web Risk detected a ' . $threatType . ' threat'
        //         ];
        //     }
        // }


        // Use VirusTotal
        // $virusTotalResult = $this->virusTotalService->checkMaliciousUrl($url);
        // if ($virusTotalResult !== null) {
        //     $maliciousCount = $virusTotalResult['virus_total_malicious_count'];
        //     return [
        //         'trust_score' => 0,
        //         'virus_total_malicious_count' => $maliciousCount
        //     ];
        // }
        return [
            'trust_score' => 1000,
        ];
    }

}

<?php


namespace App\Services;

use DateTime;
use App\Services\ScanLayers\Whois;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\BadDomainCheck;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RemoveWww;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\ScanLayers\LevenshteinAlgorithm;
use App\Services\UrlManipulations\RedirectionValue;
use App\Services\UrlManipulations\SubdomainExtract;


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
        RedirectionValue $redirectionValue,
        LevenshteinAlgorithm $levenshteinAlgorithm,
        SubdomainExtract $subdomainExtract,
        BadDomainCheck $badDomainlist,
        WhoIs $whois
    ) {
        $this->webRiskService = $webRiskService;
        $this->virusTotalService = $virusTotalService;
        $this->ipChecker = $ipChecker;
        $this->removeWWW = $removeWWW;
        $this->redirectionValue = $redirectionValue;
        $this->levenshteinAlgorithm = $levenshteinAlgorithm;
        $this->subdomainExtract = $subdomainExtract;
        $this->badDomainlist = $badDomainlist;
        $this->whois = $whois;
    }
    public function evaluateTrust($url)
    {
        //removing www from all ulrs
        $modifiedUrl = $this->removeWWW->removeWWW($url);

        // if ($this->whois->isDomainLessThanAWeekOld($modifiedUrl)) {
        //     return 'less than';
        // }
        //handeling urls with IP address
        if ($this->ipChecker->isIpAddress($modifiedUrl)) {
            return [
                'trust_score' => 0,
                'reason' => 'IP address detected. Only domain names are allowed.'
            ];
        }
        //looking in to the bad domain list:
        if ($this->badDomainlist->isDomainInJson($modifiedUrl)) {
            return [
                'trust_score' => 0,
                'reason' => 'in the bad domain list (malwares from urlH)'
            ];
        }
        //if($this->badDomainlist->isDomainInJson($modifiedUrl))
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
        if ($similarDomain = $this->levenshteinAlgorithm->compareDomains($modifiedUrl)) {
            return [
                'trust_score' => 800,
                'reason' => "similar domain found"
            ];
        }
        $sslEntery = parse_url($modifiedUrl, PHP_URL_HOST);
        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($sslEntery);
        $output = shell_exec($command);
        $sslCheckResult = json_decode($output, true);
        if ($sslCheckResult['resolved'] == false) {
            return [
                'trust_score' => 0,
                'reason' => "ssl certification didnt resolve"
            ];
        } elseif ($sslCheckResult['resolved'] == true && $sslCheckResult['days_left'] < 1) {
            return [
                'trust_score' => 0,
                'reason' => "ssl certification expired"
            ];
        }
        //performing a whois:
        $creationDate = $this->whois->getDomainInfo($modifiedUrl)['Domain created'];
        $creationDateTime = new DateTime($creationDate);
        $currentDateTime = new DateTime();
        $interval = $currentDateTime->diff($creationDateTime);
        if ($interval->d < 7) {
            return [
                'trust_score' => 0,
                'reason' => "domain was created less than a week ago"
            ];
        }
        //google
        $googleWebRiskResult = $this->webRiskService->checkForThreats($modifiedUrl);
        //var_dump($googleWebRiskResult);
        foreach (['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING'] as $threatType) {
            if (isset($googleWebRiskResult[$threatType]['threat']) && $googleWebRiskResult[$threatType]['threat'] !== null) {
                return [
                    'trust_score' => 0,
                    'reason' => 'Google Web Risk detected a ' . $threatType . ' threat'
                ];
            }
        }
        return [
            'trust_score' => 1000,
        ];
    }

}

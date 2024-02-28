<?php


namespace App\Services;

use DateTime;
use App\Services\ScanLayers\Whois;
use Illuminate\Support\Facades\App;
use App\Services\ScanLayers\UrlHaus;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\SubdomainEnum;
use App\Services\ScanLayers\BadDomainCheck;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RemoveWww;
use App\Services\ScanLayers\VirusTotalService;
use App\Services\UrlManipulations\HasSubdomain;
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
        WhoIs $whois,
        HasSubdomain $hasSub,
        SubdomainEnum $subEnum,
        UrlHaus $urlHaus
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
        $this->hasSub = $hasSub;
        $this->subEnum = $subEnum;
        $this->urlHaus =  $urlHaus;
    }
    public function evaluateTrust($url)
    {
        try {
            //removing www from all ulrs
            $modifiedUrl = $this->removeWWW->removeWWW($url);
            //handeling urls with IP address
            //testing

            //return $this->redirectionValue->redirectionValue($modifiedUrl);


            ///
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
            //return $this->redirectionValue->redirectionValue($modifiedUrl);
            if ($this->redirectionValue->redirectionValue($url)) {
                $redirectionChecker = base_path('app/Scripts/RedirectionCheck.sh') . ' ' . escapeshellarg($url);
                $redirectionTracker = shell_exec($redirectionChecker);
                $rediItem = json_decode($redirectionTracker)->fd;
                if (parse_url($rediItem, PHP_URL_SCHEME) !== 'https') {
                    return [
                        'trust_score' => 0,
                        'reason' => 'url Schme in redirection is not https'
                    ];
                }
            } 
            if(parse_url($modifiedUrl, PHP_URL_SCHEME) == 'http'){
                return [
                    'trust_score' => 0,
                    'reason' => 'url Schme is http'
                ];
            }
            //handeling similarities to famouse domain names:
            //return $similarDomain = $this->levenshteinAlgorithm->compareDomains($modifiedUrl);
            if ($this->levenshteinAlgorithm->compareDomains($modifiedUrl)) {
                return [
                    'trust_score' => 800,
                    'reason' => "similar domain found"
                ];
            }
            //checking for ssl/tls props:
            $ssl_whois_Entery = parse_url($modifiedUrl, PHP_URL_HOST);
            $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($ssl_whois_Entery);
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
            //if there is a subdomain:
            if ($this->hasSub->hasSubdomain($modifiedUrl)) {
                $whoisCheck = $this->subdomainExtract->extractSubdomainsFromUrl($modifiedUrl)['domain'];
                $subExtract = $this->subdomainExtract->extractSubdomainsFromUrl($modifiedUrl)['subdomains'];
                $subDomainlist = $this->subEnum->subdomainEnum($subExtract);
                $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($whoisCheck);
                $command = shell_exec($whoisCountry);
                $output = json_decode($command);
                //return $output->register_country;
                $creationDate = $this->whois->getDomainInfo($whoisCheck)['Domain created'];
                //var_dump($this->whois->getDomainInfo(['data']));
                $creationDateTime = new DateTime($creationDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($creationDateTime);
                if ($subDomainlist === false || $interval->d < 7 || ($output->register_country !== 'GB' && $output->register_country !== 'US')) {
                    return [
                        'trust_score' => 0,
                        'reason' => "domain was created less than a week ago or not in UK/US or not in the validated subdomains list"
                    ];
                }
                //in case of no subdomain for whois:
                $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($ssl_whois_Entery);
                $command = shell_exec($whoisCountry);
                $output = json_decode($command);
                $creationDate = $this->whois->getDomainInfo($modifiedUrl)['Domain created'];
                // var_dump($this->whois->getDomainInfo(['data']));
                $creationDateTime = new DateTime($creationDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($creationDateTime);
                if ($interval->d < 7 || ($output->register_country !== 'GB' && $output->register_country !== 'US')) {
                    return [
                        'trust_score' => 0,
                        'reason' => "domain was created less than a week ago or not in UK/US"
                    ];
                }

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
            if($this->urlHaus->queryUrl($url)){
                return [
                    'trust_score' => 0,
                    'reason' => 'urlHaus detected an online and active Malware'
                ];
            }
            return [
                'trust_score' => 1000,
            ];
        } catch (\Exception $e) {
            if ($e instanceof SomeKnownException) {
                return [
                    'trust_score' => 500,
                    'reason' => $e->getMessage(),
                ];
            }
            return [
                'trust_score' => 500,
                'reason' => 'unknown',
            ];
        }
    }
}

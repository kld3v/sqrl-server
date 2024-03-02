<?php


namespace App\Services;

use DateTime;
use SpecificException;
use AnotherSpecificException;
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
use App\Services\UrlManipulations\StringEntropy;
use App\Services\ScanLayers\LevenshteinAlgorithm;
use App\Services\UrlManipulations\RedirectionValue;
use App\Services\UrlManipulations\SubdomainExtract;



class EvaluateTrustService
{
    public function __construct(
        private GoogleWebRisk $webRiskService,
        private IpChecker $ipChecker,
        private RemoveWww $removeWWW,
        private RedirectionValue $redirectionValue,
        private LevenshteinAlgorithm $levenshteinAlgorithm,
        private SubdomainExtract $subdomainExtract,
        private BadDomainCheck $badDomainlist,
        private WhoIs $whois,
        private HasSubdomain $hasSub,
        private SubdomainEnum $subEnum,
        private UrlHaus $urlHaus,
    ) {
    }
    public function evaluateTrust($url)
    {
        try {
            //removing www from all ulrs
            $modifiedUrl = $this->removeWWW->removeWWW($url);

            /*
            Nathan wrote these
            $checks = [
                'checkIpOk',
                'checkBadDomain'
            ];

            A collection of the different results from the given checks
            $checkResults = [];
            foreach($checks as $check)
            {
                $checkResult = $this->{ $check }($modifiedUrl);
                $checkResults[] = $checkResult;
                if ($checkResult->fails())
                    break;
            }
            return $this->resolveFinalResult($checkResults);
            */

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

            } else {
                if (parse_url($modifiedUrl, PHP_URL_SCHEME) == 'http') {
                    return [
                        'trust_score' => 0,
                        'reason' => 'url Schme is http'
                    ];
                }
            }
            //handeling similarities to famouse domain names:
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
                //.uk cases:
                if (strpos($url, '.uk') !== false) {
                    $Uk_sub_subExtract = $this->subdomainExtract->extractSubdomainsFromUrl($url)['subdomains'];
                    $subDomainlist = $this->subEnum->subdomainEnum($Uk_sub_subExtract);
                    $whoisDateCheck_UK = $this->subdomainExtract->extractSubdomainsFromUrl($url)['domain'];
                    $creationDate = $this->whois->getDomainInfo($whoisDateCheck_UK)['Domain created'];
                    $creationDateTime = new DateTime($creationDate);
                    $currentDateTime = new DateTime();
                    $interval = $currentDateTime->diff($creationDateTime);
                    if ($interval->days < 7) {
                        return [
                            'trust_score' => 0,
                            'reason' => '.uk case with less than a week age or bad domain name'
                        ];
                    }
                } else {
                    //has subdomain but there is no .uk
                    $whoisCheck = $this->subdomainExtract->extractSubdomainsFromUrl($modifiedUrl)['domain'];
                    $subExtract = $this->subdomainExtract->extractSubdomainsFromUrl($modifiedUrl)['subdomains'];
                    $subDomainlist = $this->subEnum->subdomainEnum($subExtract);
                    $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($whoisCheck);
                    $command = shell_exec($whoisCountry);
                    $output = json_decode($command);
                    //return $output->register_country;
                    $stringEntropy = new StringEntropy();
                    $entropyResult = $stringEntropy->calculateEntropy($subExtract);
                    $creationDate = $this->whois->getDomainInfo($whoisCheck)['Domain created'];
                    //var_dump($this->whois->getDomainInfo(['data']));
                    $creationDateTime = new DateTime($creationDate);
                    $currentDateTime = new DateTime();
                    $interval = $currentDateTime->diff($creationDateTime);
                    if ($entropyResult > 3 || $interval->days < 7 || ($output->register_country !== 'GB' && $output->register_country !== 'US')) {
                        return [
                            'trust_score' => 0,
                            'reason' => "domain was created less than a week ago or not in UK/US or has high entropy"
                        ];
                    }
                }
            }
            //no subdomain:::
            if ($this->hasSub->hasSubdomain($modifiedUrl) === false) {
                $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($ssl_whois_Entery);
                $command = shell_exec($whoisCountry);
                $output = json_decode($command);
                $creationDate = $this->whois->getDomainInfo($modifiedUrl)['Domain created'];
                // var_dump($this->whois->getDomainInfo(['data']));
                $creationDateTime = new DateTime($creationDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($creationDateTime);
                //very bad logic here for google/yahoo and etc--->these cases dont have country in their info
                if ($interval->days < 7 || ($output->register_country !== 'GB' && $output->register_country !== 'US' && $output->register_country !== '')) {
                    return [
                        'trust_score' => 0,
                        'reason' => "domain was created less than a week ago or not in UK/US"
                    ];
                }
                //.uk cases with no subdomains::
                if (strpos($url, '.uk') !== false) {
                    $creationDate = $this->whois->getDomainInfo($modifiedUrl)['Domain created'];
                    $creationDateTime = new DateTime($creationDate);
                    $currentDateTime = new DateTime();
                    $interval = $currentDateTime->diff($creationDateTime);
                    if ($interval->days < 7) {
                        return [
                            'trust_score' => 0,
                            'reason' => "domain in ithe UK but and was created less than a week ago"
                        ];
                    }
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
            //urlHaus
            if ($this->urlHaus->queryUrl($url)) {
                return [
                    'trust_score' => 0,
                    'reason' => 'urlHaus detected an online and active Malware'
                ];
            }
            return [
                'trust_score' => 1000,
            ];
        } catch (\Exception $exception) {
            if ($exception instanceof someKnownExention) {
                return [
                    'trust_score' => 500,
                    'reason' => $exception->getMessage(),
                ];
            }
            return [
                'trust_score' => 500,
                'reason' => 'unknown'
            ];
        } catch (\Throwable $throwable) {
            // Catch any other throwable that might not be Exception -> this catches everything.
            return [
                'trust_score' => 500,
                'reason' => 'unknown - Big error'
            ];
        }
    }


    // See above - consider refactoring checks into separate functions
    //Nathan wrote these
    private function checkIpOk($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();

        if ($this->ipChecker->isIpAddress($url))
            $trustScore->setScore(0, 'IP address detected. Only domain names are allowed.');

        return $trustScore;
    }

    private function resolveFinalResult($results)
    {
        $finalResult = new TrustScoreResult(0);
        $totalWeights = 0;
        foreach ($results as $result) {
            $finalResult->setScore(
                $finalResult->getScore() + $result->getWeightedScore()
            );
            $finalResult->addReasons($result->getReasons());

            $totalWeights += $result->getWeight();
        }

        if ($totalWeights)
            $finalResult->setScore($finalResult->getScore() / $totalWeights);

        return $finalResult;
    }
}

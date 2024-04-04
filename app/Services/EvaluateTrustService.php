<?php


namespace App\Services;

use DateTime;
use App\Services\ScanLayers\Whois;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Services\ScanLayers\UrlHaus;
use App\Http\Controllers\URLController;
use App\Http\Controllers\ScanController;
use App\Services\ScanLayers\GoogleWebRisk;
use App\Services\ScanLayers\SubdomainEnum;
use App\Services\ScanLayers\BadDomainCheck;
use App\Services\UrlManipulations\IpChecker;
use App\Services\UrlManipulations\RemoveWww;
use App\Services\ScanLayers\DomainReputation;
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
        private DomainReputation $domainReputation
    ) {
    }
    public function evaluateTrust($url)
    {
        $wwwUrl = $url;
        $url = $this->removeWWW->removeWWW($url);

        $results = [];
    
        $checks = [
            fn() => $this->checkIpOk($url),
            fn() => $this->checkDomainInBadList($url),
            fn() => $this->checkSchemeIsHttps($url),
            fn() => $this->checkDomainSimilarity($url),
            fn() => $this->checkSslCertificate($url),
            fn() => $this->checkSubdomainDetails($wwwUrl, $url),
            fn() => $this->checkGoogleWebRisk($url),
            fn() => $this->checkDomainReputation($wwwUrl),
            fn() => $this->checkUrlHaus($wwwUrl),
        ];
    
        foreach ($checks as $check) {
            $result = $check();
            $results[] = $result;
    
            // If the score of a check is 0, stop executing further checks.
            if ($result->getScore() === 0) {
                break;
            }
        }
    
        return $this->resolveFinalResult($results);
    }
    
    private function checkIpOk($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();

        if ($this->ipChecker->isIpAddress($url))
            $trustScore->setScore(0, 'IP address detected. Only domain names are allowed.');

        return $trustScore;
    }

    private function checkDomainInBadList($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        if ($this->badDomainlist->isDomainInJson($url)) {
            $trustScore->setScore(0, 'Domain is in the bad domain list.');
        }
        return $trustScore;
    }

    private function checkSchemeIsHttps($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        if (parse_url($url, PHP_URL_SCHEME) != 'https') {
            $trustScore->setScore(0, 'URL scheme is not HTTPS.');
        }
        return $trustScore;
    }

    private function checkDomainSimilarity($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        if ($this->levenshteinAlgorithm->compareDomains($url)) {
            $trustScore->setScore(100, 'Similar domain found.');
        } 
        return $trustScore;
    }

    private function checkSslCertificate($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        $sslWhoisEntry = parse_url($url, PHP_URL_HOST);
        $command = base_path('app/Scripts/Sslkey.sh') . ' ' . escapeshellarg($sslWhoisEntry);
        $output = shell_exec($command);
        $sslCheckResult = json_decode($output, true);
        if ($sslCheckResult['resolved'] == false) {
            $trustScore->setScore(0, "SSL certification didn't resolve.");
        } elseif ($sslCheckResult['resolved'] == true && $sslCheckResult['days_left'] < 1) {
            $trustScore->setScore(0, "SSL certification expired.");
        }
        return $trustScore;
    }
    
    private function checkSubdomainDetails($wwwUrl, $url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult(); // Start with a default full score
    
        // Extract subdomains and domain
        $domainInfo = $this->subdomainExtract->extractSubdomainsFromUrl($wwwUrl);
        $subdomains = $domainInfo['subdomains'] ?? '';
        $domain = $domainInfo['domain'] ?? '';
    
        // Always calculate entropy if subdomains exist
        $entropyResult = null;
        if (!empty($subdomains)) {
            $stringEntropy = new StringEntropy();
            $entropyResult = $stringEntropy->calculateEntropy($subdomains);
        }
    
        // Check for .uk domains and their creation date
        if (strpos($wwwUrl, '.uk') !== false) {
            if (!empty($subdomains)) {
                // Handle subdomain logic for .uk domains
                $subDomainlist = $this->subEnum->subdomainEnum($subdomains);
            }
            $creationDate = $this->whois->getDomainInfo($domain)['Domain created'] ?? null;
            if ($creationDate) {
                $creationDateTime = new DateTime($creationDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($creationDateTime);
                if ($interval->days < 7 || ($entropyResult !== null && $entropyResult > 4)) {
                    $trustScore->setScore(0, '.uk domain with less than a week age or high entropy');
                    return $trustScore; // Early return for fail condition
                }
            }
        } else {
            // Handle non-.uk domains
            if ($this->hasSub->hasSubdomain($url)) {
                // Fetch WHOIS information for entropy and creation date checks
                $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($domain);
                $command = shell_exec($whoisCountry);
                $whoisResult = json_decode($command, true);
                
                $creationDate = $this->whois->getDomainInfo($domain)['Domain created'] ?? null;
                if ($creationDate) {
                    $creationDateTime = new DateTime($creationDate);
                    $currentDateTime = new DateTime();
                    $interval = $currentDateTime->diff($creationDateTime);
                    if ($entropyResult > 4 || $interval->days < 7) {
                        $trustScore->setScore(0, "Domain was created less than a week ago/high entropy subdomain");
                        return $trustScore;
                    }
                }
            }
        }
    
        // Handle domains with no subdomains
        if (!$this->hasSub->hasSubdomain($url)) {
            // Fetch WHOIS information for domains without subdomains
            $whoisCountry = base_path('app/Scripts/Whois.sh') . ' ' . escapeshellarg($domain);
            $command = shell_exec($whoisCountry);
            $whoisResult = json_decode($command, true);
            
            $creationDate = $this->whois->getDomainInfo($domain)['Domain created'] ?? null;
            if ($creationDate) {
                $creationDateTime = new DateTime($creationDate);
                $currentDateTime = new DateTime();
                $interval = $currentDateTime->diff($creationDateTime);
                if ($interval->days < 7) {
                    $trustScore->setScore(0, "Domain was created less than a week ago.");
                    return $trustScore;
                }
            }
        }
    
        return $trustScore;
    }
    


    private function checkGoogleWebRisk($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        $googleWebRiskResult = $this->webRiskService->checkForThreats($url);
        foreach (['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING'] as $threatType) {
            if (isset($googleWebRiskResult[$threatType]['threat']) && $googleWebRiskResult[$threatType]['threat'] !== null) {
                $trustScore->setScore(0, "Google Web Risk detected a $threatType threat.");
                return $trustScore;
            }
        }
        return $trustScore;
    }

    private function checkDomainReputation($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        $domainRepInfo = $this->domainReputation->domain_reputation_check($url);
        $domainRepScore = $domainRepInfo['reputationScore'];
        if ($domainRepScore < 60.0) {
            $reason = 'Domain reputation is below 60%';
            if ($domainRepScore >= 45.0) {
                $trustScore->setScore(800, "$reason but higher than or equal to 45%");
            } else {
                $trustScore->setScore(450, $reason);
            }
        }
        return $trustScore;
    }
    private function checkUrlHaus($url): TrustScoreResult
    {
        $trustScore = new TrustScoreResult();
        if ($this->urlHaus->queryUrl($url)) {
            $trustScore->setScore(0, "URLHaus detected an online and active malware.");
        }
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

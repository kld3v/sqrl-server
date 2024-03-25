<?php

namespace App\Services\ScanLayers;

use Illuminate\Support\Facades\Log;

class DomainReputation
{
    protected $apiKey;
    protected $requestCount;
    protected $startTime;

    public function __construct()
    {
        $this->apiKey = config('services.Domain_reputation_key.domain_rep_key');
        $this->requestCount = 0;
        $this->startTime = microtime(true);
    }

    public function domain_reputation_check($url)
    {
        $currentTime = microtime(true);
        $elapsedTime = $currentTime - $this->startTime;

        // If more than a minute has elapsed, reset the request count and start time
        if ($elapsedTime >= 60) {
            $this->requestCount = 0;
            $this->startTime = $currentTime;
        }

        // Check if the request count exceeds the limit
        if ($this->requestCount >= 100) {
            // If the limit is exceeded, calculate the time to wait before making a new request
            $timeToWait = ceil(60 - $elapsedTime);
            sleep($timeToWait);
            // Reset request count and start time after waiting
            $this->requestCount = 0;
            $this->startTime = microtime(true);
        }

        $apiUrl = 'https://api.threatintelligenceplatform.com/v2/reputation';
        $parameters = array(
            'domainName' => $url,
            'mode' => 'fast',
            'apiKey' => $this->apiKey
        );

        $queryString = http_build_query($parameters);
        $apiUrl .= '?' . $queryString;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $repResult = curl_exec($ch);

        curl_close($ch);
        $this->requestCount++;

        // Check for errors
        if ($repResult === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }
        $domainData = json_decode($repResult,true);
        Log::channel('domainrepLog')->info("for this {$url} data:{$repResult}");
        return $domainData;
    }
}
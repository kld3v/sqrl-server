<?php
namespace App\Services\ScanLayers;

class DomainReputation
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.Domain_reputation_key.domain_rep_key');
    }

    public function domain_reputation_check($url)
    {
        $apiUrl = 'https://api.threatintelligenceplatform.com/v2/reputation';
        $parameters = array(
            'domainName' => $url,
            'mode' => 'full',
            'apiKey' => $this->apiKey
        );
        
        // Build the query string
        $queryString = http_build_query($parameters);

        // Final URL with query string
        $apiUrl .= '?' . $queryString;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $repResult = curl_exec($ch);

        // Check for errors
        if(curl_errno($ch)){
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);
        $response=json_decode($repResult,true);
        return $response;
    }
}

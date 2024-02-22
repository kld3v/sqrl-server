<?php

namespace App\Services\ScanLayers;

class GoogleWebRisk
{
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiKey = env("WEB_RISK_API_KEY");
    }

    public function checkForThreats($uri)
    {
        $apiUrl = 'https://webrisk.googleapis.com/v1/uris:search';
        $threatTypes = ['UNWANTED_SOFTWARE', 'MALWARE', 'SOCIAL_ENGINEERING','SOCIAL_ENGINEERING_EXTENDED_COVERAGE'];
        $responses = [];
    
        $ch = curl_init();
    
        foreach ($threatTypes as $threatType) {
            $url = $apiUrl . '?threatTypes=' . $threatType . '&uri=' . urlencode($uri) . '&key=' . $this->apiKey;
    
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
    
            if (curl_errno($ch)) {
                echo 'Curl error for ' . $threatType . ': ' . curl_error($ch) . PHP_EOL;
            } else {
                $decodedResponse = json_decode($response, true);
                $responses[$threatType] = $decodedResponse;
            }
        }
        curl_close($ch);
    
        return $responses;
    }
}

<?php
// app/Providers/Services/VirusTotalService.php

namespace App\Services\ScanLayers;

use GuzzleHttp\Client;

class VirusTotalService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('VIRUS_TOTAL_PUBLIC_KEY');
        $this->baseUrl = 'https://www.virustotal.com/api/v3/';
    }

    public function scanUrl($url)
    {
        $client = new Client();

        $response = $client->post($this->baseUrl . 'urls', [
            'headers' => [
                'x-apikey' => $this->apiKey,
            ],
            'form_params' => [
                'url' => $url,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getAnalysisDetails($analysisId)
    {
        $client = new Client();

        $response = $client->get($this->baseUrl . 'analyses/' . $analysisId, [
            'headers' => [
                'x-apikey' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function checkMaliciousUrl($url)
    {
        $virusTotalResult = $this->scanUrl($url);

        $analysisId = $virusTotalResult['data']['id'];
        $analysisDetails = $this->getAnalysisDetails($analysisId);
        $maliciousCount = isset($analysisDetails['data']['attributes']['stats']['malicious'])
            ? $analysisDetails['data']['attributes']['stats']['malicious']
            : 0;
        if ($maliciousCount > 0) {
            return [
                'security_score' => 0,
                'virus_total_malicious_count' => $maliciousCount,
            ];
        } else {
            return null;
        }
    }
}

<?php
// app/Services/VirusTotalService.php

namespace App\Providers\Services;

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
}

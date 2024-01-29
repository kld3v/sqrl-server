<?php

namespace App\Services\ScanLayers;

class GoogleWebRisk
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env("WEB_RISK_API_KEY");
    }

    public function getThreatTypes()
    {
        return [
            'UNWANTED_SOFTWARE',
            'MALWARE',
            'SOCIAL_ENGINEERING',
            'SOCIAL_ENGINEERING_EXTENDED_COVERAGE',
        ];
    }
    public function checkForThreats($uri)
    {
        $threatTypes = $this->getThreatTypes();
        $apiUrl = 'https://webrisk.googleapis.com/v1/uris:search';

        foreach ($threatTypes as $threatType) {
            $url = $apiUrl . '?threatTypes=' . $threatType . '&uri=' . urlencode($uri) . '&key=' . $this->apiKey;

            try {
                $response = $this->makeApiRequest($url);
                $result = json_decode($response, true);

                if (isset($result['matches']) && count($result['matches']) > 0) {
                    return [
                        'threat_detected' => true,
                        'body' => $result,
                    ];
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return ['threat_detected' => false];
    }
    private function makeApiRequest($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $info = curl_getinfo($ch);

        curl_close($ch);

        return $response;
    }
}

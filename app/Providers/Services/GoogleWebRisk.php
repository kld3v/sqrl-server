<?php

namespace App\Providers\Services;
class GoogleWebRisk
{
    public function checkWebRisk($uri, $apiKey)
    {
        $apiUrl = 'https://webrisk.googleapis.com/v1/uris:search';

        $threatTypes = [
            'MALWARE',
            'SOCIAL_ENGINEERING',
            'UNWANTED_SOFTWARE',
            'SOCIAL_ENGINEERING_EXTENDED_COVERAGE',
        ];

        $correctResult = null;
        $results = [];

        foreach ($threatTypes as $threatType) {
            $url = $apiUrl . '?threatTypes=' . $threatType . '&uri=' . urlencode($uri) . '&key=' . $apiKey;

            try {
                $response = $this->makeApiRequest($url);

                $result = json_decode($response, true);

                if (isset($result['matches']) && count($result['matches']) > 0) {
                    $correctResult = [
                        'body' => $result,
                    ];
                    break;
                } elseif (isset($result['matches']) && count($result['matches']) == 0) {
                    continue;
                } else {
                    $results[$threatType] = [
                        'body' => $result,
                    ];
                }
            } catch (\Exception $e) {
                $results[$threatType] = [
                    'error' => $e->getMessage(),
                ];
            }
        }

        $resultsWithBody = array_filter($results, function ($result) {
            return isset($result['body']) && !empty($result['body']);
        });

        $finalResult = $correctResult ?? $resultsWithBody;

        return $finalResult;
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

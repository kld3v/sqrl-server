<?php

namespace App\Services\ScanLayers;

class UrlHaus
{
    public function queryUrl($url)
    {
        $apiUrl = "https://urlhaus-api.abuse.ch/v1/url/";
        $data = ['url' => $url];

        $ch = curl_init($apiUrl);
        $this->setCurlOptions($ch, $data);

        $res = curl_exec($ch);

        $error = $this->checkCurlError($ch);
        if ($error !== null) {
            return $error;
        }

        curl_close($ch);

        $response = json_decode($res, true);

        return $this->isValidResponse($response);
    }

    private function setCurlOptions($ch, $data)
    {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    private function checkCurlError($ch)
    {
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            return "cURL Error: $error";
        }

        return null;
    }

    private function isValidResponse($response)
    {
        if (
            isset($response['url_status']) &&
            isset($response['threat']) &&
            $response['url_status'] === 'online' &&
            $response['threat'] === 'malware_download'
        ) {
            return true;
        }

        return false;
    }
}

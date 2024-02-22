<?php
namespace App\Services\UrlManipulations;

class RedirectionValue
{
    public function redirectionValue($url)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_exec($curl);

        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpStatusCode >= 300 && $httpStatusCode < 400) {
           return true;
        } else {
            return false;
        }
    }
}
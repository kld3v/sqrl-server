<?php
namespace App\Services\UrlManipulations;
use Illuminate\Support\Facades\Log;

class RedirectionValue
{
    public function redirectionValue($url)
    {
        // Log::channel('redirectLog')->debug("Checking redirection status for URL: {$url}");

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_exec($curl);

        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // Log::channel('redirectLog')->debug("HTTP status code for URL {$url}: {$httpStatusCode}");

        // var_dump('curl status is' . $httpStatusCode);
        if ($httpStatusCode >= 300 && $httpStatusCode < 400) {
            // Log::channel('redirectLog')->info("URL {$url} is a redirect.");
           return true;
        } else {
            // Log::channel('redirectLog')->info("URL {$url} is not a redirect.");
            return false;
        }
    }
}
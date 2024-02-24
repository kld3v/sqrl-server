<?php
namespace App\Services\UrlManipulations;

class SubdomainExtract
{
    public function extractSubdomainsFromUrl($url)
    {
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $url, $matches)) {
            $domain = $matches['domain'];

            // Extract subdomains
            $subdomains = rtrim(strstr($url, $domain, true), '.');

            // Return domain and subdomains as an array
            return [
                'domain' => $domain,
                'subdomains' => $subdomains,
            ];
        } else {
            // Return the original URL if no valid domain is found
            return [
                'domain' => $url,
                'subdomains' => null,
            ];
        }
    }
}

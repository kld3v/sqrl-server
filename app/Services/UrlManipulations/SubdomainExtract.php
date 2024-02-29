<?php
namespace App\Services\UrlManipulations;

class SubdomainExtract
{
    public function extractSubdomainsFromUrl($url)
    {
        $cleanedURL = $this->removePathFromUrl($url);
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $cleanedURL, $matches)) {
            $domain = $matches['domain'];

            $subdomains = rtrim(strstr($url, $domain, true), '.');

            return [
                'domain' => $domain,
                'subdomains' => $subdomains,
            ];
        }
    }
    private function removePathFromUrl($url) {
        $parsedUrl = parse_url($url);
    
        if ($parsedUrl && isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            $cleanUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    
            if (isset($parsedUrl['port'])) {
                $cleanUrl .= ':' . $parsedUrl['port'];
            }
    
            return $cleanUrl;
        }
    
        return $url;
    }
}

<?php


namespace App\Services\UrlResolver;

class UrlResolver
{
    public function resolveUrl($url)
    {
        $parsedUrl = parse_url($url);

        $scheme = $parsedUrl['scheme'] ?? null;
        $host = $parsedUrl['host'] ?? '';

        $domain = $this->extractDomain($host);
        $subdomain = $this->extractSubdomains($host);

        return [
            'scheme' => $scheme,
            'domain' => $domain,
            'subdomain' => $subdomain,
        ];
    }
    private function extractDomain($host)
    {
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $host, $matches)) {
            return $matches['domain'];
        } else {
            return $host;
        }
    }

    private function extractSubdomains($host)
    {
        $subdomains = $host;
        $domain = $this->extractDomain($subdomains);

        $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

        return $subdomains;
    }
}

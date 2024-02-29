<?php
namespace App\Services\ScanLayers;

class SubdomainEnum
{
    private $commonSubdomains = [
        'www',
        'mail',
        'ftp',
        'blog',
        'webmail',
        'server',
        'ns1',
        'ns2',
        'ns3',
        'ns4',
        'smtp',
        'shop',
        'dev',
        'app',
        'support',
        'api'
    ];

    public function subdomainEnum( $subDomains)
    {
        // Extract the host from the URL
        $parsedUrl = parse_url($subDomains);
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $inputSubdomains = explode('.', $subDomains);

        if ($host) {
            array_unshift($inputSubdomains, $host);
        }

        foreach ($inputSubdomains as $subdomain) {
            $subdomain = preg_replace("#^https?://#", '', $subdomain);
            if (!in_array($subdomain, $this->commonSubdomains)) {
                return false;
            }
        }

        return true;
    }
    public function enumerateSubdomains($domain)
    {
        $subdomains = array();
        // Check common subdomains like www, mail, ftp, etc.
        foreach ($this->commonSubdomains as $commonSubdomain) {
            $subdomainToCheck = $commonSubdomain . '.' . $domain;
            if (dns_get_record($subdomainToCheck, DNS_A)) {
                $subdomains[] = $subdomainToCheck;
            }
        }
        return $subdomains;
    }
}
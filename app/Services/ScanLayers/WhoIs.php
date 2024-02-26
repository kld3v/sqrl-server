<?php
// app/Services/WhoisService.php

namespace App\Services\ScanLayers;

use Iodev\Whois\Factory;

class WhoIs
{
    protected $whois;

    public function __construct()
    {
        $this->whois = Factory::get()->createWhois();
    }

    private function cleanUrl($domain)
    {
        $urlParts = parse_url($domain);

        $urlWithoutScheme = isset($urlParts['scheme']) ? substr($domain, strlen($urlParts['scheme']) + 3) : $domain;
    
        // Remove path and query
        $cleanedDomain = strtok($urlWithoutScheme, '/?');
    
        return $cleanedDomain;
    }

    public function isDomainAvailable($domain)
    {
        $cleanedDomain = $this->cleanUrl($domain);
        return $this->whois->isDomainAvailable($cleanedDomain);
    }

    public function performRawLookup($domain)
    {
        $cleanedDomain = $this->cleanUrl($domain);
        $response = $this->whois->lookupDomain($cleanedDomain);
        return $response->text;
    }

    public function getDomainInfo($domain)
    {
        $cleanedDomain = $this->cleanUrl($domain);
        $info = $this->whois->loadDomainInfo($cleanedDomain);

       
        
        return [
            'Domain created' => date("Y-m-d", $info->creationDate),
            'Domain expires' => date("Y-m-d", $info->expirationDate),
            'Domain owner' => $info->owner,
            'data'=>$cleanedDomain
        ];
    }

}
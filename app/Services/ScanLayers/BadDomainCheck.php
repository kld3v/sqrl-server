<?php
namespace App\Services\ScanLayers;

class BadDomainCheck
{
    protected $badDomains;

    public function __construct()
    {
        $jsonFilePath = base_path('bin/badDomains.json');
        $this->badDomains = json_decode(file_get_contents($jsonFilePath), true);
    }

    public function isDomainInJson($domain)
    {
        $maliciousDomain = str_replace(array('http://', 'https://'), '', $domain);
        return in_array($maliciousDomain, $this->badDomains['domains']);    
    }
}

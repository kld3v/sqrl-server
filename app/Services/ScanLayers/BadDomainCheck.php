<?php
namespace App\Services\ScanLayers;

class BadDomainCheck
{
    protected $jsonData;

    public function __construct()
    {
        $jsonFilePath = public_path('badDomains.json');
        $this->jsonData = json_decode(file_get_contents($jsonFilePath), true);
    }

    public function isDomainInJson($domain)
    {
        $urlParts = parse_url($domain);
        $schemeRemoved = substr($domain, strlen($urlParts['scheme']) + 3);
        
        return in_array($schemeRemoved, $this->jsonData['domains']);
    }
}

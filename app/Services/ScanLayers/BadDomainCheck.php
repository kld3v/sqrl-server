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
        $maliciousDomain = parse_url($domain, PHP_URL_HOST);
        return in_array($maliciousDomain, $this->jsonData['domains']);
    }
}

<?php
//this class checkes againts the urls from urlHaus which is mostly malware related ->in a json file in public folder
namespace App\Services\UrlResolver;

class BadDomainChecker
{
    private $badDomains = [];

    public function __construct()
    {
        $this->loadBadDomainsFromJson();
    }

    public function checkDomain($url)
    {
        $cleanedUrl = $this->cleanUrl($url);
        return in_array($cleanedUrl, $this->badDomains);
    }

    private function cleanUrl($url)
    {
        $parsedUrl = parse_url($url);

        $cleanedUrl = '';

        if (isset($parsedUrl[PHP_URL_SCHEME])) {
            $cleanedUrl .= $parsedUrl[PHP_URL_SCHEME] . '://';
        }

        if (isset($parsedUrl['host'])) {
            $cleanedUrl .= $parsedUrl['host'];
        }

        if (isset($parsedUrl[PHP_URL_PORT])) {
            $cleanedUrl .= ':' . $parsedUrl[PHP_URL_PORT];
        }

        if (isset($parsedUrl[PHP_URL_PATH])) {
            $cleanedUrl .= $parsedUrl[PHP_URL_PATH];
        }

        if (isset($parsedUrl[PHP_URL_QUERY])) {
            $cleanedUrl .= '?' . $parsedUrl[PHP_URL_QUERY];
        }

        if (isset($parsedUrl[PHP_URL_FRAGMENT])) {
            $cleanedUrl .= '#' . $parsedUrl[PHP_URL_FRAGMENT];
        }

        return $cleanedUrl;
    }

    private function loadBadDomainsFromJson()
    {
        $jsonFileData = public_path('badUrls.json');

        if (file_exists($jsonFileData)) {
            $jsonFileContent = file_get_contents($jsonFileData);

            $jsonData = json_decode($jsonFileContent, true);

            if (isset($jsonData['domains'])) {
                $this->badDomains = $jsonData['domains'];
            } else {
                $this->badDomains = [];
            }
        } else {
            $this->badDomains = [];
        }
    }
}
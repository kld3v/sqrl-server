<?php
namespace App\Services\UrlResolver;


class UrlRedirectionChecker
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function checkRedirection()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);

        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return $finalUrl !== $this->url;
    }
}



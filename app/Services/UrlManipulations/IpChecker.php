<?php

namespace App\Services\UrlManipulations;

class IpChecker
{
    public function isIpAddress($url)
    {
        $urlComponents = parse_url($url);
        return isset($urlComponents['host']) && filter_var($urlComponents['host'], FILTER_VALIDATE_IP) !== false;
    }
}

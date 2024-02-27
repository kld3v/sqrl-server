<?php
namespace App\Services\UrlManipulations;

class HasSubdomain {
    
    private $url;

    public function hasSubdomain($url,) {
        $urlParts = parse_url($url);
        
        if (isset($urlParts['scheme'], $urlParts['host'])) {
            $hostParts = explode('.', $urlParts['host']);
            if (count($hostParts) > 2) {
                return true;
            }
        }
        return false;
    }
}

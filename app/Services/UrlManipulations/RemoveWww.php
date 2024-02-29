<?php 
namespace App\Services\UrlManipulations;
class RemoveWww {
    public function removeWWW($url)
    {
        $urlComponents = parse_url($url);

        if (isset($urlComponents['host'])) {
            $urlComponents['host'] = preg_replace('/^www\./', '', $urlComponents['host']);
        }

        $modifiedUrl = '';
        if (isset($urlComponents['scheme'])) {
            $modifiedUrl .= $urlComponents['scheme'] . '://';
        }
        if (isset($urlComponents['host'])) {
            $modifiedUrl .= $urlComponents['host'];
        }
        if (isset($urlComponents['path'])) {
            $modifiedUrl .= $urlComponents['path'];
        }
        if (isset($urlComponents['query'])) {
            $modifiedUrl .= '?' . $urlComponents['query'];
        }
        if (isset($urlComponents['fragment'])) {
            $modifiedUrl .= '#' . $urlComponents['fragment'];
        }

        return $modifiedUrl;
    }
}
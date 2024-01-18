<?php

namespace App\Services\ShortUrl;

use App\Services\ShortUrl\shortURL_services;
use App\Services\ShortUrl\resolvers\Generic;

class shortURL_smain
{
    protected $shortUrlServices;
    protected $genericResolver;

    public function __construct(shortURL_services $shortUrlServices)
    {
        $this->shortUrlServices = $shortUrlServices;
        $this->genericResolver = new Generic();
    }
    public function isShortUrl($url)
    {
        return $this->shortUrlServices->which_service($url) !== null;
    }
    public function unshorten($url, $timeout = null)
    {   
        $service = $this->shortUrlServices->which_service($url);

        switch ($service) {
            case "adf.ly":
            case "atominik.com":
            case "fumacrom.com":
            case "intamema.com":
            case "j.gs":
            case "q.gs":
                // return $this->resolvers->adfly->unshort($url, $timeout)
                return false;
            case "gns.io":
            case "ity.im":
            case "ldn.im":
            case "nowlinks.net":
            case "rlu.ru":
            case "tinyurl.com":
            case "tr.im":
            case "u.to":
            case "vzturl.com":
                // return $this->resolvers->redirect->unshort($url, $timeout);
                return false; //very doable
            case "cutt.us":
            case "soo.gd":
                // return $this->resolvers->refresh->unshort($url, $timeout);
                return false; //Very doable

            case "adfoc.us":
                // return $this->resolvers->adfocus->unshort($url, $timeout);
                return false; //maybe doable
            case "shorturl.at":
                // return $this->resolvers->shorturl->unshort($url, $timeout);
                return false; //maybe doable
            case "surl.li":
                // return $this->resolvers->surlli->unshort($url, $timeout);
                return false; //maybe doable
            default:
                // return $this->resolvers->generic->unshort($url, $timeout);
                return $this->genericResolver->unshort($url, $timeout);
        }
    }
}
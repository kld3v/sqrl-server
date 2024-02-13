<?php


namespace App\Services\ShortURL;
use App\Services\ShortURL\resolvers\Generic;

class ShortURLMain
{
    private $shortUrlServices;
    private $genericResolver;

    public function __construct(ShortURLServices $shortUrlServices)
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
            // case "adf.ly":
            // case "atominik.com":
            // case "fumacrom.com":
            // case "intamema.com":
            // case "j.gs":
            // case "q.gs":
            //     // return $this->resolvers->adfly->unshort($url, $timeout)
            //     return false;
            // case "ity.im":
            // case "ldn.im":
            // case "nowlinks.net":
            // case "rlu.ru":
            // // case "tinyurl.com":
            // case "tr.im":
            // case "u.to":
            // case "vzturl.com":
            //     // return $this->resolvers->redirect->unshort($url, $timeout);
            //     return false; //very doable
            // case "cutt.us":
            // case "soo.gd":
            //     // return $this->resolvers->refresh->unshort($url, $timeout);
            //     //I THINK THIS BOTH NOW INACTIVE??
            //     return false; //Very doable

            // case "adfoc.us":
            //     // return $this->resolvers->adfocus->unshort($url, $timeout);
            //     //I THINK THIS IS NOW INACTIVE??
            //     return false;
            default:
                // return $this->resolvers->generic->unshort($url, $timeout);
                return $this->genericResolver->unshort($url, $timeout);
        }
    }
}

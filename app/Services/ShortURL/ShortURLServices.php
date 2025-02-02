<?php

namespace App\Services\ShortUrl;

use Illuminate\Support\Facades\Log;

class ShortURLServices
{
    private $services = [
        "adf.ly",
        "adfoc.us",
        "amzn.to",
        "atominik.com",
        "ay.gy",
        "b.link",
        "bhpho.to",
        "bit.ly",
        "bit.do",
        "bn.gy",
        "branch.io",
        "buff.ly",
        "ceesty.com",
        "chollo.to",
        "cli.re",
        "cli.fm",
        "cutt.ly",
        "cutt.us",
        "db.tt",
        "f.ls",
        "fa.by",
        "fb.me",
        "flip.it",
        "fumacrom.com",
        "geni.us",
        "git.io",
        "goo.gl",
        "gns.io",
        "hmm.rs",
        "ht.ly",
        "hyperurl.co",
        "is.gd",
        "intamema.com",
        "ity.im",
        "j.gs",
        "j.mp",
        "kutt.it",
        "ldn.im",
        "linklyhq.com",
        "microify.com",
        "mzl.la",
        "nmc.sg",
        "nowlinks.net",
        "ow.ly",
        "plu.sh",
        "prf.hn",
        "q.gs",
        "qr.ae",
        "qr.net",
        "rb.gy",
        "rebrand.ly",
        "rlu.ru",
        "rotf.lol",
        "s.coop",
        "s.id",
        "sh.st",
        "soo.gd",
        "short.gy",
        "shortcm.xyz",
        "shorturl.at",
        "smu.sg",
        "smq.tc",
        "snip.ly",
        "snipr.com",
        "snipurl.com",
        "snurl.com",
        "split.to",
        "surl.li",
        "t.co",
        "t.ly",
        "t2m.io",
        "tiny.cc",
        "tiny.pl",
        "tinyium.com",
        "tinyurl.com",
        "tiny.one",
        "tny.im",
        "tny.sh",
        "tr.im",
        "trib.al",
        "u.to",
        "v.gd",
        "virg.in",
        "vzturl.com",
        "waa.ai",
        "washex.am",
        "x.co",
        "y2u.be",
        "yourwish.es",
        "zpr.io",
        ];


    // Check and tell which URL Shortener Service is used
    public function which_service($url)
    {

        foreach ($this->services as $service) {
            if (stripos($url, $service)) {
                return $service;
            }
        }
        return null;
    }
}

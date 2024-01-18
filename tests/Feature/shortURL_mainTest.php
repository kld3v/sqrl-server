<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ShortUrl\shortURL_main;
use App\Services\ShortUrl\shortURL_services;

class shortURL_mainTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // can identify if a URL is a short URL
    public function test_can_identify_if_url_is_short_url()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);
    
        $url = "https://t.ly/3uTvn";
        $result = $shortURL_main->isShortUrl($url);
    
        $this->assertTrue($result);
    }

    public function test_can_unshorten_tly()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);

        $shortUrl = "https://t.ly/3uTvn";
        $expectedUrl = "https://en.wikipedia.org/wiki/Bassarona_teuta";

        $result = $shortURL_main->unshorten($shortUrl);

        

        $this->assertEquals($expectedUrl, $result);
    }

    public function test_can_unshorten_genius()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);

        $shortUrl = "https://geni.us/iWD3K";
        $expectedUrl = "https://www.amazon.co.uk/?tag=ragnbonebrown-21&linkId=c0e1d3e2ab70a516d89d04a1a2919291&ref_=as_li_ss_tl&geniuslink=true";

        $result = $shortURL_main->unshorten($shortUrl);

        

        $this->assertEquals($expectedUrl, $result);
    }

    public function test_can_unshorten_surl()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);

        $shortUrl = "http://surl.li/plryd";
        $expectedUrl = "https://www.keychron.com/pages/k6-pro-user-manual";

        $result = $shortURL_main->unshorten($shortUrl);

        

        $this->assertEquals($expectedUrl, $result);
    }
    public function test_can_unshorten_shorturl()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);

        $shortUrl = "https://shorturl.at/EO146";
        $expectedUrl = "https://en.wikipedia.org/wiki/Bassarona_teuta";

        $result = $shortURL_main->unshorten($shortUrl);

        

        $this->assertEquals($expectedUrl, $result);
    }
    public function test_can_unshorten_tinyurl()
    {
        $shortUrlServices = new shortURL_services();
        $shortURL_main = new shortURL_main($shortUrlServices);

        $shortUrl = "http://tinyurl.com/3kh353xn";
        $expectedUrl = "https://en.wikipedia.org/wiki/Bassarona_teuta";

        $result = $shortURL_main->unshorten($shortUrl);

        

        $this->assertEquals($expectedUrl, $result);
    }
 
}


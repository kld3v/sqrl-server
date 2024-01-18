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
    // Test that the which_service method returns the correct shortener service when given a URL containing that service
    public function test_returns_correct_shortener_service()
    {
        $shortURL = new shortURL_services();
        $url = "https://t.ly/QAlRP";
        $expectedService = "t.ly";
        
        $result = $shortURL->which_service($url);
        
        $this->assertEquals($expectedService, $result);
    }
}

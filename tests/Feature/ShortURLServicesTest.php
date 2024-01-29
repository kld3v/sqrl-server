<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ShortUrl\ShortURLMain;
use App\Services\ShortUrl\ShortURLServices;

class ShortURLServicesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Test that the which_service method returns the correct shortener service when given a URL containing that service
    // Test that the which_service method returns the correct shortener service when given a URL containing that service
    public function test_returns_correct_shortener_service()
    {
        $shortURL = new ShortURLServices();
        $url = "https://goo.gl/abc123";
        $expectedService = "goo.gl";

        $result = $shortURL->which_service($url);

        $this->assertEquals($expectedService, $result);
    }

        // Test that the which_service method returns null when given a URL that does not contain any known shortener service
    public function test_returns_null_for_unknown_shortener_service()
    {
        $shortURL = new ShortURLServices();
        $url = "https://example.com";

        $result = $shortURL->which_service($url);

        $this->assertNull($result);
    }

        // Test that the which_service method is case-insensitive when matching shortener services
    public function test_is_case_insensitive()
    {
        $shortURL = new ShortURLServices();
        $url = "https://GoO.gL/abc123";
        $expectedService = "goo.gl";

        $result = $shortURL->which_service($url);

        $this->assertEquals($expectedService, $result);
    }

        // Test that the which_service method can handle a URL that is null
    public function test_handles_null_url()
    {
        $shortURL = new ShortURLServices();
        $url = null;

        $result = $shortURL->which_service($url);

        $this->assertNull($result);
    }

        // Test that the which_service method can handle a URL that is an empty string
    public function test_handles_empty_string_url()
    {
        $shortURL = new ShortURLServices();
        $url = "";

        $result = $shortURL->which_service($url);

        $this->assertNull($result);
    }

        // Test that the which_service method can handle a URL that is a single character
    public function test_handles_single_character_url()
    {
        $shortURL = new ShortURLServices();
        $url = "a";

        $result = $shortURL->which_service($url);

        $this->assertNull($result);
    }
}

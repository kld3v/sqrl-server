<?php

namespace Tests\Unit;

use App\Models\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

use Tests\TestCase;

class URLModelTest extends TestCase
{
    /** @test */
    public function test_create_new_url_instance_with_valid_parameters()
    {
        $url = new URL([
            'URL' => 'https://example.com',
            'trust_score' => 80,
        ]);
    
        $this->assertInstanceOf(URL::class, $url);
        $this->assertEquals('https://example.com', $url->URL);
        $this->assertEquals(80, $url->trust_score);
    }

        // Saving a new URL instance to the database should succeed.
    public function test_save_new_url_instance_to_database()
    {
        $url = new URL([
            'URL' => 'https://example.com',
            'trust_score' => 80,
        ]);

        $this->assertTrue($url->save());
        $this->assertDatabaseHas('URLs', [
            'URL' => 'https://example.com',
            'trust_score' => 80,
        ]);
    }
        // Retrieving an existing URL instance from the database by its ID should succeed.
        public function test_retrieve_existing_url_instance_from_database()
        {
            $url = URL::find(1);
        
            $this->assertInstanceOf(URL::class, $url);
            $this->assertEquals('https://example.com', $url->URL);
            $this->assertEquals(80, $url->trust_score);
        }
            // Creating a new URL instance with an invalid URL parameter should fail.
    public function test_create_new_url_instance_with_invalid_url_parameter()
    {
        $this->expectException(ValidationException::class);

        $url = new URL([
            'URL' => 'invalid_url',
            'trust_score' => 80,
        ]);
    }
        // Creating a new URL instance with a negative trust_score parameter should fail.
        public function test_create_new_url_instance_with_negative_trust_score_parameter()
        {
            $this->expectException(ValidationException::class);
        
            $url = new URL([
                'URL' => 'https://example.com',
                'trust_score' => -10,
            ]);
        }

            // Creating a new URL instance with a trust_score greater than 100 should fail.
    public function test_create_new_url_instance_with_greater_than_1000_trust_score_parameter()
    {
        $this->expectException(ValidationException::class);

        $url = new URL([
            'URL' => 'https://example.com',
            'trust_score' => 4141432,
        ]);
    }
}

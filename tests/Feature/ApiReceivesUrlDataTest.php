<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiReceivesUrlDataTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

        // The receiveUrl method should correctly retrieve the 'url' input from the request body and return a JSON response with the same value.
    public function test_retrieve_url_from_request_body()
    {
        $response = $this->postJson('/api/receiveUrlData', ['url' => 'http://example.com']);

        $response
            ->assertStatus(200)
            ->assertJson(['Database_Response' => 'http://example.com']);
    }
}

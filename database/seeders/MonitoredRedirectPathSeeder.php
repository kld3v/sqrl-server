<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\MonitoredRedirectPath;

class MonitoredRedirectPathSeeder extends Seeder
{
    public function run()
    {
        $venue = Venue::first(); // Assuming there's at least one venue from VenueSeeder

        // Expected correct redirection
        MonitoredRedirectPath::create([
            'venue_id' => $venue->id,
            'initial_url' => 'https://tinyurl.com/yxakfket',
            'expected_url' => 'https://en.wikipedia.org/wiki/Shekel'
        ]);

        // Expected incorrect redirection (to simulate failure)
        MonitoredRedirectPath::create([
            'venue_id' => $venue->id,
            'initial_url' => 'https://tinyurl.com/yxakfket',
            'expected_url' => 'https://example.com' // intentionally incorrect
        ]);
    }
}
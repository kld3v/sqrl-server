<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First, create or find the URL record
        $url = URL::firstOrCreate(['url' => 'https://alacrityfoundation.co.uk/']);

        // Now, create the Venue record
        Venue::create([
            'company' => 'Alacrity',
            'chain' => 'Newport',
            'url_id' => $url->id,
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.5877159 -2.9931069, 51.5878166 -2.9928685, 51.5876624 -2.9926144, 51.5875762 -2.9928780, 51.5877159 -2.9931069))')")
        ]);

        Venue::create([
            'company' => 'Alacrity',
            'chain' => 'Newport',
            'url_id' => $url->id,
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.5877159 -2.9931069, 51.5878166 -2.9928685, 51.5876624 -2.9926144, 51.5875762 -2.9928780, 51.5877159 -2.9931069))')")
        ]);
    }
}

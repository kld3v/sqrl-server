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
        $alacrityUrl = URL::firstOrCreate(['url' => 'https://alacrityfoundation.co.uk/', 'trust_score' => 0]);

        Venue::create([
            'company' => 'Alacrity',
            'chain' => 'Newport',
            'url_id' => $alacrityUrl->id,
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.5877159 -2.9931069, 51.5878166 -2.9928685, 51.5876624 -2.9926144, 51.5875762 -2.9928780, 51.5877159 -2.9931069))')")
        ]);

        $nandosUrl = URL::firstOrCreate(['url' => 'https://www.nandos.co.uk/eat-in-landing/newport-friars-walk', 'trust_score' => 100]);

        Venue::create([
            'company' => 'Nando\'s',
            'chain' => 'Newport - Friars Walk',
            'url_id' => $nandosUrl->id,
            'tel' => '01633215205',
            'address' => '73 Usk Plaza Unit R4, Friars Walk Shopping Centre NP20 1DS',
            'postcode' => 'NP20 1DS',
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.586312850599846 -2.9922399197930285, 51.58607526800188 -2.9920521795881667, 51.58596769316683 -2.99259691005394, 51.58616280037874 -2.9927551818310656, 51.586312850599846 -2.9922399197930285))')")
        ]);
    }
}

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
        $alacrityUrl = URL::firstOrCreate([
            'url' => 'https://alacrityfoundation.co.uk/',
            'trust_score' => 0,
            'test_version' => "1.0.0"
        ]);
    
        $alacrityMidpoint = $this->calculateMidpoint([
            [51.5877159, -2.9931069],
            [51.5878166, -2.9928685],
            [51.5876624, -2.9926144],
            [51.5875762, -2.9928780],
        ]);
    
        Venue::create([
            'company' => 'Alacrity',
            'chain' => 'Newport',
            'url_id' => $alacrityUrl->id,
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.5877159 -2.9931069, 51.5878166 -2.9928685, 51.5876624 -2.9926144, 51.5875762 -2.9928780, 51.5877159 -2.9931069))')"),
            'midpoint' => DB::raw("ST_PointFromText('POINT($alacrityMidpoint[0] $alacrityMidpoint[1])')"),
            'status' => 'active'
        ]);
    
        // Repeat the process for each venue
        // Example for Nando's venue
        $nandosUrl = URL::firstOrCreate([
            'url' => 'https://www.nandos.co.uk/',
            'trust_score' => 100,
            'test_version' => "1.0.0"
        ]);
    
        $nandosMidpoint = $this->calculateMidpoint([
            [51.586312850599846, -2.9922399197930285],
            [51.58607526800188, -2.9920521795881667],
            [51.58596769316683, -2.99259691005394],
            [51.58616280037874, -2.9927551818310656],
        ]);
    
        Venue::create([
            'company' => 'Nando\'s',
            'chain' => 'Newport - Friars Walk',
            'url_id' => $nandosUrl->id,
            'tel' => '01633215205',
            'address' => '73 Usk Plaza Unit R4, Friars Walk Shopping Centre NP20 1DS',
            'postcode' => 'NP20 1DS',
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.586312850599846 -2.9922399197930285, 51.58607526800188 -2.9920521795881667, 51.58596769316683 -2.99259691005394, 51.58616280037874 -2.9927551818310656, 51.586312850599846 -2.9922399197930285))')"),
            'midpoint' => DB::raw("ST_PointFromText('POINT($nandosMidpoint[0] $nandosMidpoint[1])')"),
            'status' => 'active'
        ]);

        $sheppardURL = URL::firstOrCreate([
            'url' => 'https://assets-global.website-files.com/5ec5008dfeab8a08b7ae667a/5fad187b318b332d6b6a25d1_dave.jpg',
            'trust_score' => 0,
            'test_version' => "1.0.0"
        ]);

        $sheppardMidpoint = $this->calculateMidpoint([
            [51.81418716758371, -2.623143288559274],
            [51.81419111083795, -2.6228913008135004],
            [51.813802641100565, -2.62287216635659],
            [51.813798697995935, -2.6231369106944515],
        ]);

        Venue::create([
            'company' => 'the sheppard',
            'chain' => 'the farm',
            'url_id' => $sheppardURL->id,
            'area' => DB::raw("ST_PolygonFromText('POLYGON((51.81418716758371 -2.623143288559274, 51.81419111083795 -2.6228913008135004, 51.813802641100565 -2.62287216635659, 51.813798697995935 -2.6231369106944515, 51.81418716758371 -2.623143288559274))')"),
            'midpoint' => DB::raw("ST_PointFromText('POINT($sheppardMidpoint[0] $sheppardMidpoint[1])')")
        ]);
    
    }
    
    protected function calculateMidpoint(array $coordinates): array
    {
        $latSum = 0;
        $lonSum = 0;
        foreach ($coordinates as $coord) {
            $latSum += $coord[0];
            $lonSum += $coord[1];
        }
        $latAvg = $latSum / count($coordinates);
        $lonAvg = $lonSum / count($coordinates);
    
        return [$latAvg, $lonAvg];
    }
    
}

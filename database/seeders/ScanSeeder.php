<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scan;
use App\Models\URL;
use Faker\Factory as Faker;

class ScanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $areas = [
            ['lat' => [51.610363, 51.682787], 'long' => [-4.010980, -3.774522]],
            ['lat' => [51.468497, 51.542617], 'long' => [-3.254832, -3.105044]],
            ['lat' => [51.567424, 51.619370], 'long' => [-3.059136, -2.927484]],
            ['lat' => [51.416494, 51.515650], 'long' => [-2.655908, -2.480190]],
        ];

        $assignedLocations = []; // Array to track the assigned locations of URL IDs

        for ($i = 1; $i <= 20000; $i++) {
            $urlId = $faker->numberBetween(1, 1000);
            $url = URL::find($urlId);
            $trustScore = $url ? $url->trust_score : 0;
            $urlLastDigit = substr($urlId, -1);
            $urlFirstDigit = substr($urlId, 0, 1);
            $areaIndex = ($urlFirstDigit >= 1 && $urlFirstDigit <= 2) ? 0 :
                        (($urlFirstDigit >= 3 && $urlFirstDigit <= 5) ? 1 :
                        (($urlFirstDigit >= 6 && $urlFirstDigit <= 7) ? 2 : 3));

            if ($urlLastDigit > 6 && array_key_exists($urlId, $assignedLocations)) {
                // Cluster near the existing location for this URL ID
                $latitude = $assignedLocations[$urlId]['latitude'] + mt_rand(-10, 10) / 100000;    
                $longitude = $assignedLocations[$urlId]['longitude'] + mt_rand(-20, 20) / 100000;
            } else {
                // For other URLs, randomly distribute them within the area or if the URL is new
                $latitude = $faker->randomFloat(6, $areas[$areaIndex]['lat'][0], $areas[$areaIndex]['lat'][1]);
                $longitude = $faker->randomFloat(6, $areas[$areaIndex]['long'][0], $areas[$areaIndex]['long'][1]);
                // If the URL ID is not yet tracked, store its location
                if (!array_key_exists($urlId, $assignedLocations)) {
                    $assignedLocations[$urlId] = [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ];
                }
            
            }

                
            Scan::create([
                'url_id' => $urlId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'user_id' => $faker->numberBetween(1, 1500),
                'trust_score' => $trustScore
            ]);
        }
    }
}               
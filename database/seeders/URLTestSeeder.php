<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\URL;
use Faker\Factory as Faker;

class URLTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $insertData = [];

        foreach (range(1,1000) as $index) {
            $trustScore = (substr($index, -1) > 6) ? 1000 : $faker->numberBetween($min = 1, $max = 1000);

            $insertData[] = [
                'url' => $faker->unique()->url,
                'trust_score' => $trustScore,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }
        URL::insert($insertData);

        URL::create([
            'url' => "https://alacrityfoundation.co.uk/",
            'trust_score' => 0
        ]);
    }
}

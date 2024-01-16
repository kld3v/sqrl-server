<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\URL;
use Faker\Factory as Faker;

class URLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1,1000) as $index) {
            $trustScore = (substr($index, -1) > 6) ? 1000 : $faker->numberBetween($min = 1, $max = 1000);

            URL::create([
                'URL' => $faker->url,
                'trust_score' => $trustScore
            ]);
        }
    }
}

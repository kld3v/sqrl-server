<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('questions')->insert([
            [
                'question_text' => 'Join Our Community: Is this something you would use?',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question_text' => 'Market Place: Is this something you would use?',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

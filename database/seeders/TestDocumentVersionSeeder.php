<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDocumentVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('document_versions')->insert([
            [
                'document_name' => 'Terms and Conditions',
                'version' => "0.01",
                'document_url' => 'https://example.com/terms',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'document_name' => 'Privacy Policy',
                'version' => "0.01",
                'document_url' => 'https://example.com/privacy',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

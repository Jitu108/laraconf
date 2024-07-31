<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    public function run(): void
    {

        // Truncate venues table before seeding to avoid duplicate entries
        DB::table('venues')->truncate();

        $venues = [
            ['name' => 'Kamani Auditorium', 'city' => 'Delhi', 'country' => 'India', 'postal_code' => '110011', 'region' => 'India'],
            ['name' => 'Shri Satya Sai Auditorium', 'city' => 'Delhi', 'country' => 'India', 'postal_code' => '110019', 'region' => 'India'],
            ['name' => 'Siri Fort Auditorium', 'city' => 'Delhi', 'country' => 'India', 'postal_code' => '110011', 'region' => 'India'],
            ['name' => 'Ryman Auditorium', 'city' => 'Tennessee', 'country' => 'US', 'postal_code' => '37219', 'region' => 'US'],

        ];

        DB::table('venues')->insert($venues);
    }
}

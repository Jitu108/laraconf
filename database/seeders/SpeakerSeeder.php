<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpeakerSeeder extends Seeder
{
    public function run(): void
    {

        // Truncate speakers table before seeding to avoid duplicate entries
        DB::table('speakers')->truncate();

        $speakers = [
            ['name' => 'Jitendra Gupta', 'email' => 'jitendra.g@curiticshealth.com', 'bio' => 'Delivery Manager @ Curitics Health', 'twitter_handle' => '@jitendra'],
            ['name' => 'Amarjeet Kumar', 'email' => 'amarjeet.k@curiticshealth.com', 'bio' => 'Tech Lead @ Curitics Health', 'twitter_handle' => '@amarjeet'],
            ['name' => 'Vishal Singh', 'email' => 'vishal.s@curiticshealth.com', 'bio' => 'Developer @ Curitics Health', 'twitter_handle' => '@vishal'],
            ['name' => 'Devansh Rai', 'email' => 'devansh.rai@curiticshealth.com', 'bio' => 'Developer @ Curitics Health', 'twitter_handle' => '@devansh'],
            ['name' => 'Manoj Bharti', 'email' => 'manoj.b@curiticshealth.com', 'bio' => 'Developer @ Curitics Health', 'twitter_handle' => '@manoj'],
        ];

        DB::table('speakers')->insert($speakers);
    }
}

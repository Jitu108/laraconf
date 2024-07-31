<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        // Truncate users table before seeding to avoid duplicate entries
        DB::table('users')->truncate();

        $user = [
            [
                'name' => 'Jitendra Gupta',
                'email' => 'gupta.jitendra108@gmail.com',
                'password' => bcrypt(value: 'password'),
            ]
        ];

        DB::table('users')->insert($user);
    }
}

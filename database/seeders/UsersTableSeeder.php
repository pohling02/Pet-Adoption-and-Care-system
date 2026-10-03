<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {

    public function run(): void {
        //Disable foreign key checks to prevent constraint errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        //Clear existing data before seeding (for fresh seeding)
        DB::table('users')->truncate();

        //Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //Insert predefined users
        DB::table('users')->insert([
            [
                'name' => 'TARUMT',
                'email' => 'ng@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ling Poh',
                'email' => 'ngpl-wm22@student.tarc.edu.my',
                'password' => Hash::make('staff123'),
                'role' => 'shelter_staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poh Ling',
                'email' => 'lingpoh@icloud.com',
                'password' => Hash::make('staff123'), 
                'role' => 'shelter_staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ng Poh Ling',
                'email' => 'npl5109@gmail.com',
                'password' => Hash::make('adopter123'),
                'role' => 'adopter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ng Poh Ling',
                'email' => 'npl@gmail.com',
                'password' => Hash::make('adopter123'),
                'role' => 'adopter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShelterStaffProfileSeeder extends Seeder
{
    public function run()
    {
        // Fetch the shelter staff users from the users table
        $shelterStaff1 = DB::table('users')->where('email', 'npltarc@gmail.com')->first();
        $shelterStaff2 = DB::table('users')->where('email', 'npl@gmail.com')->first();

        if ($shelterStaff1 && $shelterStaff2) {
            DB::table('shelter_staff_profiles')->insert([
                [
                    'UserID' => $shelterStaff1->UserID,
                    'phone_number' => '012-3456789',
                    'shelter_name' => 'Happy Tails Shelter',
                    'shelter_address' => '123, Pet Street, Kuala Lumpur, Malaysia',
                    'position' => 'Manager',
                    'bio' => 'Passionate about animal welfare and pet adoptions.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'UserID' => $shelterStaff2->UserID,
                    'phone_number' => '019-9876543',
                    'shelter_name' => 'Safe Haven Animal Rescue',
                    'shelter_address' => '456, Animal Road, Penang, Malaysia',
                    'position' => 'Veterinarian',
                    'bio' => 'Dedicated vet helping rescued animals recover and find new homes.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        } else {
            echo "Shelter staff users not found. Please check user seeder or manually add them to the database.\n";
        }
    }
}

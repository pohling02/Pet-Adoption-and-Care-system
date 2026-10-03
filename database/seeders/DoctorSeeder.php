<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('doctors')->insert([
            [
                'DoctorName' => 'Dr. Will Khor',
                'DoctorEmail' => 'johndoe@example.com',
                'Specialization' => 'General Veterinary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'DoctorName' => 'Dr. Wong Han Ying',
                'DoctorEmail' => 'janesmith@example.com',
                'Specialization' => 'Surgery',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'DoctorName' => 'Dr. Lim Ji Moon',
                'DoctorEmail' => 'emilyjohnson@example.com',
                'Specialization' => 'Dentistry',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}

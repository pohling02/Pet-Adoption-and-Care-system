<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder {

    public function run() {
        // ✅ Fetch available timeslot IDs for predefined dates
        $timeslot1 = DB::table('doctor_schedules')
                ->where('DoctorID', 2)
                ->where('AvailableDate', '2025-01-01')
                ->value('ScheduleID');

        $timeslot2 = DB::table('doctor_schedules')
                ->where('DoctorID', 2)
                ->where('AvailableDate', '2025-02-10')
                ->value('ScheduleID');

        $timeslot3 = DB::table('doctor_schedules')
                ->where('DoctorID', 2)
                ->where('AvailableDate', '2025-03-25')
                ->value('ScheduleID');

        // ✅ Check if timeslots exist before inserting
        if (!$timeslot1 || !$timeslot2 || !$timeslot3) {
            $this->command->error("❌ Missing timeslot IDs. Run DoctorScheduleSeeder first.");
            return;
        }

        DB::table('appointments')->insert([
            [
                'AppointmentDate' => '2025-01-01 09:00:00',
                'Purpose' => 'Routine Health Checkup',
                'Status' => 'Expired',
                'AdopterID' => 4,
                'PetID' => 4,
                'DoctorID' => 2,
                'timeslot_id' => $timeslot1, // ✅ Assigning correct timeslot
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'AppointmentDate' => '2025-02-10 10:00:00',
                'Purpose' => 'Vaccination Booster',
                'Status' => 'Expired',
                'AdopterID' => 4,
                'PetID' => 4,
                'DoctorID' => 2,
                'timeslot_id' => $timeslot2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'AppointmentDate' => '2025-03-25 11:00:00',
                'Purpose' => 'Dental Cleaning',
                'Status' => 'Confirmed',
                'AdopterID' => 4,
                'PetID' => 4,
                'DoctorID' => 2,
                'timeslot_id' => $timeslot3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("✅ AppointmentSeeder executed successfully!");
    }
}

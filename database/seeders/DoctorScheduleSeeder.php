<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DoctorScheduleSeeder extends Seeder {
    public function run() {
        $doctors = DB::table('doctors')->pluck('DoctorID');

        // Define available time slots for each working day
        $timeslots = [
            '09:00 AM - 10:00 AM',
            '10:00 AM - 11:00 AM',
            '11:00 AM - 12:00 PM',
            '02:00 PM - 03:00 PM',
            '03:00 PM - 04:00 PM',
            '04:00 PM - 05:00 PM',
        ];

        // ✅ Ensure predefined dates are included
        $requiredDates = [
            '2025-01-01', // New Year
            '2025-02-10',
            '2025-03-25',
        ];

        foreach ($doctors as $doctorID) {
            $date = Carbon::now();
            $workingDays = 0; 

            // ✅ Generate schedules for the next 7 working days (Monday-Friday)
            while ($workingDays < 7) {
                if (!$date->isWeekend()) {
                    foreach ($timeslots as $timeslot) {
                        DB::table('doctor_schedules')->updateOrInsert(
                            [
                                'DoctorID' => $doctorID,
                                'AvailableDate' => $date->format('Y-m-d'),
                                'Timeslot' => $timeslot
                            ],
                            [
                                'IsBooked' => false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                    $workingDays++;
                }
                $date->addDay(); // Move to the next day
            }

            // ✅ Ensure required dates have schedules
            foreach ($requiredDates as $requiredDate) {
                foreach ($timeslots as $timeslot) {
                    DB::table('doctor_schedules')->updateOrInsert(
                        [
                            'DoctorID' => $doctorID,
                            'AvailableDate' => $requiredDate,
                            'Timeslot' => $timeslot
                        ],
                        [
                            'IsBooked' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}

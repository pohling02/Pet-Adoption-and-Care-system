<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\HealthRecord;
use App\Models\Pet;
use App\Models\User;

class HealthRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all shelter staff users
        $shelterStaffs = User::where('role', 'shelter_staff')->pluck('UserID')->toArray();

        if (empty($shelterStaffs)) {
            $this->command->info('No shelter staff found. Please seed users first.');
            return;
        }

        // Fetch pets assigned to shelter staff
        $pets = Pet::whereIn('ShelterID', $shelterStaffs)->get();

        if ($pets->isEmpty()) {
            $this->command->info('No pets found. Please seed pets first.');
            return;
        }

        // Define sample health data
        $healthRemarks = ['Dental problem', 'Lung problem', 'Needs deworming', 'Under treatment', 'Healthy'];
        $diagnoses = ['Parvovirus', 'Fungal Infection', 'Fleas Infestation', 'Malnutrition', 'No Issues'];
        $medications = [
            'Antibiotics', 'Deworming Pills', 'Painkillers', 'Steroids', 'None',
            'Antifungal Cream', 'Flea Treatment', 'Vitamin Supplements'
        ];

        $healthRecords = [];

        foreach ($pets as $pet) {
            $numRecords = rand(1, 4); // Each pet gets 1-4 health records

            // Ensure vaccination status is **consistent** for all records of a pet
            $vaccinationStatus = $pet->VaccinationStatus; // Use pet's existing status

            // Generate **chronological** health records
            $dates = collect();
            for ($i = 0; $i < $numRecords; $i++) {
                $dates->push(Carbon::now()->subMonths(rand(1, 12))->format('Y-m-d'));
            }
            $dates = $dates->sort(); // Sort dates to ensure logical order

            foreach ($dates as $date) {
                $healthRecords[] = [
                    'VaccinationStatus' => $vaccinationStatus, // Keep the same status for all records
                    'HealthRemarks' => $healthRemarks[array_rand($healthRemarks)],
                    'Sterilization' => ['Neutered', 'Not Neutered'][array_rand(['Neutered', 'Not Neutered'])],
                    'Diagnosis' => $diagnoses[array_rand($diagnoses)], // ✅ New field
                    'Medicine' => $medications[array_rand($medications)], // ✅ New field
                    'LastCheckupDate' => $date,
                    'PetID' => $pet->PetID,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert health records into the database
        DB::table('health_records')->insert($healthRecords);

        $this->command->info('Health records seeded successfully with Diagnosis & Medicine included!');
    }
}

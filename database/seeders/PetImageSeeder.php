<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pet;

class PetImageSeeder extends Seeder {

    public function run(): void {
        $pets = Pet::all()->keyBy('PetID');

        $petImagesData = [
            1 => [
                'images/Asher1.jpg',
                'images/Asher2.jpg',
                'images/Asher3.jpg',
                'images/Asher4.jpg',
            ],
            2 => [
                'images/Bella1.jpg',
                'images/Bella2.jpg',
            ],
            3 => [
                'images/Endo1.jpg',
                'images/Endo2.jpg',
                'images/Endo3.jpg',
                'images/Endo4.jpg',
                'images/Endo5.jpg',
            ],
            4 => [
                'images/Baby1.jpg',
                'images/Baby2.jpg',
            ],
            5 => [
                'images/Charcoal1.jpg',
                'images/Charcoal2.jpg',
            ],
            
            6 => [
                'images/Daisy1.jpg',
                'images/Daisy2.jpg',
                'images/Daisy3.jpg',
            ],
            7 => [
                'images/Kiara1.jpg',
                'images/Kiara2.jpg',
                'images/Kiara3.jpg',
                'images/Kiara4.jpg',
            ],
            8 => [
                'images/love1.jpg',
            ],
            9 => [
                'images/Mark1.jpg',
                'images/Mark2.jpg',
                'images/Mark3.jpg',
            ],
            10 => [
                'images/Max1.jpg',
                'images/Max2.jpg',
                'images/Max3.jpg',
                'images/Max4.jpg',
            ],
            11 => [
                'images/Mochi1.jpg',
                'images/Mochi2.jpg',
                'images/Mochi3.jpg',
            ],
            12 => [
                'images/Osborn1.jpg',
                'images/Osborn2.jpg',
            ],
            13 => [
                'images/Sam1.jpg',
                'images/Sam2.jpg',
                'images/Sam3.jpg',
                'images/Sam4.jpg',
            ],
            14 => [
                'images/Poppy1.jpg',
                'images/Poppy2.jpg',
                'images/Poppy3.jpg',
            ],
            15 => [
                'images/Felicia1.jpg',
                'images/Felicia2.jpg',
            ],
            16 => [
                'images/Fufu1.jpg',
                'images/Fufu2.jpg',
                'images/Fufu3.jpg',
                'images/Fufu4.jpg',
            ],
            17 => [
                'images/Ginger1.jpg',
                'images/Ginger2.jpg',
                'images/Ginger3.jpg',
                'images/Ginger4.jpg',
            ],
            18 => [
                'images/Manxi1.jpg',
                'images/Manxi2.jpg',
                'images/Manxi3.jpg',
            ],
            19 => [
                'images/Hugo1.jpg',
                'images/Hugo2.jpg',
                'images/Hugo3.jpg',
            ],
            20 => [
                'images/Maymay1.jpg',
                'images/Maymay2.jpg',
            ],
            21 => [
                'images/Midnight1.jpg',
                'images/Midnight2.jpg',
                'images/Midnight3.jpg',
                'images/Midnight4.jpg',
            ],
            22 => [
                'images/Mimi1.jpg',
                'images/Mimi2.jpg',
                'images/Mimi3.jpg',
                'images/Mimi4.jpg',
            ],
            23 => [
                'images/Oreo1.jpg',
                'images/Oreo2.jpg',
                'images/Oreo3.jpg',
            ],
        ];

        $petImages = [];

        foreach ($petImagesData as $petID => $images) {
            if (isset($pets[$petID])) {
                foreach ($images as $imagePath) {
                    $petImages[] = [
                        'PetID' => $petID, // Directly use PetID
                        'ImagePath' => $imagePath,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert predefined pet images into the database
        DB::table('pet_images')->insert($petImages);
    }
}

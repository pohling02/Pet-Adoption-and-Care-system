<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Pet;
use App\Models\AdopterProfile;

class PetController extends Controller {

    public function show($id) {
        $pet = Pet::with([
                    'shelter',
                    'images',
                    'adoptionApplications' => function ($query) {
                        $query->where('AdopterID', auth()->id());
                    }
                ])
                ->where('PetID', $id)
                ->firstOrFail();
        return view('Adopter.pets.pet_details', compact('pet'));
    }

    public function petProfile() {
        $user = Auth::user();
        $profile = AdopterProfile::firstOrCreate(
                ['UserID' => $user->UserID],
                ['profile_picture' => 'images/blankprofile.jpg'] 
        );

        $pets = Pet::where('AdopterID', $user->UserID)
                ->with(['healthRecords.images'])  
                ->get();

        if ($pets->count() === 1) {
            return view('Adopter.pets.pet_profile', [
                'pet' => $pets->first(),
                'profile' => $profile, 
            ]);
        } else {
            return view('Adopter.pets.pet_profile_list', [
                'pets' => $pets,
                'profile' => $profile,
            ]);
        }
    }

    public function petDetails($id) {
        $pet = Pet::with(['healthRecords.images'])->findOrFail($id);
        $user = Auth::user();

        $profile = AdopterProfile::firstOrCreate(
                ['UserID' => $user->UserID],
                ['profile_picture' => 'images/blankprofile.jpg']
        );

        return view('Adopter.pets.pet_profile_details', compact('pet', 'profile'));
    }

    public function recommendPets() {
        $userID = Auth::id();
        $adopterProfile = AdopterProfile::where('UserID', $userID)->first();

        if (!$adopterProfile) {
            return redirect()->back()->with('error', 'Profile not found.');
        }

        $petPreference = $adopterProfile->pet_preference;
        $preferredLocation = $adopterProfile->preferred_location;
        $personalityPreference = strtolower($adopterProfile->bio);

        $recommendedPets = Pet::where('Species', $petPreference)
                ->where('CurrentLocation', $preferredLocation)
                ->where('AdoptionStatus', 'available')
                ->get()
                ->map(function ($pet) use ($personalityPreference) {
                    similar_text(strtolower($pet->Personality), $personalityPreference, $similarity);
                    $pet->similarityScore = $similarity; 
                    return $pet;
                })
                ->sortByDesc('similarityScore'); 

        return view('Adopter.dashboard', compact('recommendedPets'));
    }
}

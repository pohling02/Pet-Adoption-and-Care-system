<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ShelterStaffProfile;
use App\Models\AdopterProfile;
use App\Models\AdoptionApplication;

class UserProfileController extends Controller {

    public function showShelterProfile() {
        $user = Auth::user();
        $profile = ShelterStaffProfile::where('UserID', $user->UserID)->first();

        return view('ShelterStaff.profile.show', compact('user', 'profile'));
    }

    public function editShelterProfile() {
        $user = Auth::user();
        $profile = ShelterStaffProfile::where('UserID', $user->UserID)->first();

        return view('ShelterStaff.profile.edit', compact('user', 'profile'));
    }

    public function updateShelterProfile(Request $request) {
        $user = Auth::user();

        // Use firstOrCreate so `$profile` is never null
        $profile = ShelterStaffProfile::firstOrCreate(
                ['UserID' => $user->UserID],
                [
                    'profile_picture' => null,
                    'phone_number' => null,
                    'shelter_name' => null,
                    'shelter_address' => null,
                    'position' => null,
                    'bio' => null,
                ]
        );

        // Now proceed with validation, updating, etc.
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update(['name' => $request->name]);

        // Step 3: Handle profile picture
        if ($request->has('remove_profile_picture')) {
            if ($profile->profile_picture && file_exists(public_path($profile->profile_picture))) {
                unlink(public_path($profile->profile_picture));
            }
            $profile->profile_picture = null;
        } elseif ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture && file_exists(public_path($profile->profile_picture))) {
                unlink(public_path($profile->profile_picture));
            }
            $profilePictureName = time() . '_profile_picture.' .
                    $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move(public_path('images'), $profilePictureName);
            $profile->profile_picture = 'images/' . $profilePictureName;
        }

        // Step 4: Update other fields
        $profile->update($request->except(['name', 'profile_picture', 'remove_profile_picture']));

        return redirect()->route('shelter.profile.view')->with('success', 'Profile updated successfully!');
    }

    public function showAdopterProfile() {
        $user = Auth::user();

        // Ensure adopter profile exists
        $profile = AdopterProfile::firstOrCreate(
                ['UserID' => $user->UserID],
                [
                    'phone_number' => null,
                    'gender' => null,
                    'address' => null,
                    'occupation' => null,
                    'pet_preference' => null,
                    'preferred_location' => null,
                    'bio' => null,
                    'profile_picture' => 'images/blankprofile.jpg', 
                    'activity_level' => null,
                    'allergies'=> null,
                    'hours_per_day' => null,
                    'other_pets' => null,
                    'home_type' => null,
                ]
        );

        // Fetch adoption applications related to this user
        $adoptions = AdoptionApplication::where('AdopterID', $user->UserID)->get();

        return view('Adopter.profile.show', [
            'user' => $user,
            'profile' => $profile,
            'profile_picture' => asset($profile->profile_picture ?? 'images/blankprofile.jpg'),
            'adopted_pets_count' => $adoptions->where('AdoptionStatus', 'Approved')->count(),
            'pending_applications_count' => $adoptions->whereIn('AdoptionStatus', ['Pending', 'Under Review'])->count(),
        ]);
    }

    public function editAdopterProfile() {
        $user = Auth::user();
        $profile = AdopterProfile::where('UserID', $user->UserID)->first();

        if (!$profile) {
            return redirect()->route('adopter.profile.view')->with('error', 'Profile not found.');
        }

        return view('Adopter.profile.edit', compact('user', 'profile'));
    }

    public function updateAdopterProfile(Request $request) {
        $user = Auth::user();
        $profile = AdopterProfile::where('UserID', $user->UserID)->first();

        if (!$profile) {
            return redirect()->route('adopter.profile.view')->with('error', 'Profile not found.');
        }

        // Validate Request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:Male,Female,Other,Prefer not to say',
            'address' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'pet_preference' => 'required|string|max:255',
            'preferred_location' => 'required|string|max:255',
            'bio' => 'required|string',
            'profile_picture' => 'required|image|mimes:jpeg,png|max:2048', 
            'activity_level' => 'required|string|max:255',
            'allergies'=> 'required|string|max:255',
            'hours_per_day' => 'required|numeric|min:1|max:24',
            'other_pets' => 'required|string|max:255',
            'home_type' => 'required|string|max:255',
        ]);

        // Update User's Name
        $user->update(['name' => $request->name]);

        // Handle Profile Picture Removal or Upload
        if ($request->has('remove_profile_picture')) {
            if ($profile->profile_picture && file_exists(public_path($profile->profile_picture))) {
                unlink(public_path($profile->profile_picture));
            }
            // Set to default profile picture
            $profile->profile_picture = 'images/blankprofile.jpg';
        } elseif ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture && file_exists(public_path($profile->profile_picture)) && $profile->profile_picture !== 'images/blankprofile.jpg') {
                unlink(public_path($profile->profile_picture));
            }
            $profilePictureName = time() . '_profile.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move(public_path('images'), $profilePictureName);
            $profile->profile_picture = 'images/' . $profilePictureName;
        }

        // Update Other Profile Fields
        $profile->gender = $request->gender;
        $profile->phone_number = $request->phone_number;
        $profile->address = $request->address;
        $profile->occupation = $request->occupation;
        $profile->pet_preference = $request->pet_preference;
        $profile->preferred_location = $request->preferred_location;
        $profile->bio = $request->bio;
        $profile->activity_level = $request->activity_level;
        $profile->home_type = $request->home_type;
        $profile->other_pets = $request->other_pets;
        $profile->allergies = $request->allergies;
        $profile->hours_per_day = $request->hours_per_day;

        $profile->save();

        return redirect()->route('adopter.profile.view')->with('success', 'Profile updated successfully!');
    }

    public function showAdopterBriefProfile($id) {
        $adopter = User::findOrFail($id);
        $profile = AdopterProfile::where('UserID', $adopter->UserID)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return view('Adopter.profile.brief', compact('adopter', 'profile'));
    }
}

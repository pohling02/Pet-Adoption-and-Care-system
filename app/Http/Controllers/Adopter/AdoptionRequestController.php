<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pet;
use App\Models\AdoptionApplication;
use App\Models\AdopterProfile;
use App\Models\AdoptionApplicationPhoto;

class AdoptionRequestController extends Controller {

    public function index() {
        $user = Auth::user();
        $userId = Auth::id();

        $profile = AdopterProfile::firstOrCreate(
                ['UserID' => $userId],
                ['profile_picture' => null]
        );

        $adoptions = AdoptionApplication::with(['pet'])
                ->where('AdopterID', $userId)
                ->orderBy('ApplicationDate', 'desc')
                ->get();
        $approved_count = $adoptions->where('AdoptionStatus', 'Approved')->count();
        $pending_count = $adoptions->where('AdoptionStatus', 'Pending')->count();
        $rejected_count = $adoptions->where('AdoptionStatus', 'Rejected')->count();
        $under_review_count = $adoptions->where('AdoptionStatus', 'Under Review')->count();
        $cancelled_count = $adoptions->where('AdoptionStatus', 'Cancelled')->count();
        return view('Adopter.adoption.index', compact('adoptions', 'approved_count', 'pending_count', 'rejected_count', 
                'under_review_count', 'profile', 'user', 'cancelled_count'));
    }
    
    public function show($id) {
        $user = Auth::user();
        $adoption = AdoptionApplication::with(['pet.images'])->findOrFail($id);
        $profile = AdopterProfile::where('UserID', $user->UserID)->first();
        return view('Adopter.adoption.details', compact('adoption', 'profile'));
    }

    public function create($petID) {
        $pet = Pet::findOrFail($petID);
        return view('Adopter.adoption.adoption_form', compact('pet'));
    }

    public function store(Request $request, $petID) {
        $pet = Pet::findOrFail($petID);

        try {
            $validatedData = $request->validate([
                'FullName' => 'required|string|max:255',
                'Address' => 'required|string|max:500',
                'Postcode' => 'required|string|postcode_state',
                'City' => 'required|string|max:100',
                'State' => 'required|string|max:100',
                'Email' => 'required|email|max:255',
                'PhoneNo' => 'required|string|max:15',
                'Age' => 'required|integer|min:18|max:100',
                'Gender' => 'required|string|in:Male,Female,Other',
                'Occupation' => 'nullable|string|max:255',
                'HouseholdDetails' => 'nullable|string|max:500',
                'OtherPetsInfo' => 'nullable|string|max:500',
                'ReasonForAdoption' => 'nullable|string|max:500',
                'PetCarePlan' => 'nullable|string|max:500',
                'EmergencyPlan' => 'nullable|string|max:500',
                'SpayNeuterAgreement' => 'required|in:1',
                'ReturnAgreement' => 'required|in:1',
                'ReferenceName' => 'nullable|string|max:255',
                'ReferenceContact' => 'nullable|string|max:15',
                'ReferenceRelationship' => 'nullable|string|max:255',
                'LivingEnvironmentPhotos' => 'array',
                'LivingEnvironmentPhotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput();
        }
        $application = AdoptionApplication::create([
            'AdopterID' => Auth::id(),
            'PetID' => $pet->PetID,
            'ApplicationDate' => now(),
            'AdoptionStatus' => 'Pending',
            'FullName' => $request->FullName,
            'Address' => $request->Address,
            'Postcode' => $request->Postcode,
            'City' => $request->City,
            'State' => $request->State,
            'Email' => $request->Email,
            'ContactNumber' => $request->PhoneNo,
            'Age' => $request->Age,
            'Gender' => $request->Gender,
            'Occupation' => $request->Occupation,
            'HouseholdDetails' => $request->HouseholdDetails,
            'OtherPetsInfo' => $request->OtherPetsInfo,
            'ReasonForAdoption' => $request->ReasonForAdoption,
            'PetCarePlan' => $request->PetCarePlan,
            'EmergencyPlan' => $request->EmergencyPlan,
            'SpayNeuterAgreement' => $request->SpayNeuterAgreement,
            'ReturnAgreement' => $request->ReturnAgreement,
            'HomeCheckCompleted' => false,
            'ReferenceName' => $request->ReferenceName,
            'ReferenceContact' => $request->ReferenceContact,
            'ReferenceRelationship' => $request->ReferenceRelationship,
        ]);

        if ($request->hasFile('LivingEnvironmentPhotos')) {
            foreach ($request->file('LivingEnvironmentPhotos') as $file) {
                $imagePath = $file->store('uploads/living_environment', 'public');
                AdoptionApplicationPhoto::create([
                    'ApplicationID' => $application->ApplicationID,
                    'PhotoPath' => $imagePath,
                ]);
            }
        }
        return redirect()->route('adopter.adoption')
                        ->with('success', 'Adoption request submitted successfully!');
    }

    public function resubmitForm($id) {
        $adoption = AdoptionApplication::with('pet')->findOrFail($id);

        if ($adoption->AdoptionStatus !== 'Rejected') {
            return redirect()->route('adopter.adoption')->with('error', 'Only rejected applications can be resubmitted.');
        }

        return view('Adopter.adoption.resubmit', compact('adoption'));
    }

    public function resubmit(Request $request, $id) {
        $adoption = AdoptionApplication::findOrFail($id);
        //Only rejected applications can be resubmitted
        if ($adoption->AdoptionStatus !== 'Rejected') {
            return redirect()->route('adopter.adoption')->with('error', 'Only rejected applications can be resubmitted.');
        }
        //Check if the maximum resubmission limit (3) is reached
        if ($adoption->ResubmissionCount >= 3) {
            return redirect()->route('adopter.adoption')->with('error', 'You have reached the maximum number of resubmissions.');
        }
        //Check if 7 days have passed since the last rejection
        if ($adoption->LastRejectionDate && \Carbon\Carbon::parse($adoption->LastRejectionDate)->diffInDays(now()) < 7) {
            return redirect()->route('adopter.adoption')->with('error', 'You must wait 7 days before resubmitting.');
        }
        //Validate the incoming request data
        $validatedData = $request->validate([
            'FullName' => 'required|string|max:255',
            'Address' => 'required|string|max:500',
            'Postcode' => 'required|string|postcode_state',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:100',
            'Email' => 'required|email|max:255',
            'PhoneNo' => 'required|string|max:15',
            'Age' => 'required|integer|min:18|max:100',
            'Gender' => 'required|string|in:Male,Female,Other',
            'Occupation' => 'nullable|string|max:255',
            'HouseholdDetails' => 'nullable|string|max:500',
            'OtherPetsInfo' => 'nullable|string|max:500',
            'ReasonForAdoption' => 'required|string|max:500', // or nullable if you prefer
            'PetCarePlan' => 'required|string|max:500', // or nullable if you prefer
            'EmergencyPlan' => 'nullable|string|max:500',
            'SpayNeuterAgreement' => 'required|in:1',
            'ReturnAgreement' => 'required|in:1',
            'ReferenceName' => 'nullable|string|max:255',
            'ReferenceContact' => 'nullable|string|max:15',
            'ReferenceRelationship' => 'nullable|string|max:255',
            'LivingEnvironmentPhotos' => 'nullable|array',
            'LivingEnvironmentPhotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $adoption->update([
            'FullName' => $request->input('FullName'),
            'Address' => $request->input('Address'),
            'Postcode' => $request->input('Postcode'),
            'City' => $request->input('City'),
            'State' => $request->input('State'),
            'Email' => $request->input('Email'),
            'ContactNumber' => $request->input('PhoneNo'),
            'Age' => $request->input('Age'),
            'Gender' => $request->input('Gender'),
            'Occupation' => $request->input('Occupation'),
            'HouseholdDetails' => $request->input('HouseholdDetails'),
            'OtherPetsInfo' => $request->input('OtherPetsInfo'),
            'ReasonForAdoption' => $request->input('ReasonForAdoption'),
            'PetCarePlan' => $request->input('PetCarePlan'),
            'EmergencyPlan' => $request->input('EmergencyPlan'),
            'SpayNeuterAgreement' => $request->input('SpayNeuterAgreement'),
            'ReturnAgreement' => $request->input('ReturnAgreement'),
            'ReferenceName' => $request->input('ReferenceName'),
            'ReferenceContact' => $request->input('ReferenceContact'),
            'ReferenceRelationship' => $request->input('ReferenceRelationship'),
            'AdoptionStatus' => 'Under Review',
            'ResubmissionCount' => $adoption->ResubmissionCount + 1, // Increment
            'LastRejectionDate' => now(), // Mark new rejection date
            'updated_at' => now(),
        ]);

        if ($request->hasFile('LivingEnvironmentPhotos')) {
            AdoptionApplicationPhoto::where('ApplicationID', $adoption->ApplicationID)->delete();
            foreach ($request->file('LivingEnvironmentPhotos') as $file) {
                $imagePath = $file->store('uploads/living_environment', 'public');
                AdoptionApplicationPhoto::create([
                    'ApplicationID' => $adoption->ApplicationID,
                    'PhotoPath' => $imagePath,
                ]);
            }
        }
        return redirect()->route('adopter.adoption')
                        ->with('success', 'Application has been resubmitted and is now under review.');
    }

    public function cancel(Request $request, $id) {
        $adoption = AdoptionApplication::findOrFail($id);

        if ($adoption->AdopterID != Auth::id()) {
            return redirect()->route('adopter.adoption')
                            ->with('error', 'You do not have permission to cancel this application.');
        }

        if (!in_array($adoption->AdoptionStatus, ['Pending', 'Under Review'])) {
            return redirect()->route('adopter.adoption')
                            ->with('error', 'Only pending or under review applications can be cancelled.');
        }

        $request->validate([
            'cancellationReason' => 'required|string|min:5|max:500',
        ]);

        $adoption->update([
            'AdoptionStatus' => 'Cancelled',
            'CancellationReason' => $request->cancellationReason
        ]);

        return redirect()->route('adopter.adoption')
                        ->with('success', 'Your adoption application has been cancelled successfully.');
    }
}

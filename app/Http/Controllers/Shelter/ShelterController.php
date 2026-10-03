<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\AdoptionApplication;
use App\Models\HealthRecord;
use App\Models\Message;
use App\Models\Appointment;
use App\Models\PetResource;
use App\Models\Notification;
use App\Models\PetHealthRecordImage;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\AdoptionRejectedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ShelterController extends Controller {

    public function home() {
        $userId = Auth::id();
        $pets = Pet::where('ShelterID', $userId)->get();
        $petCount = $pets->count();
        $availablePets = Pet::where('ShelterID', $userId)
                ->whereRaw("LOWER(AdoptionStatus) = 'available'")
                ->count();
        $pendingAdoptions = AdoptionApplication::join('pets', 'adoption_applications.PetID', '=', 'pets.PetID')
                ->where('pets.ShelterID', $userId)
                ->where('adoption_applications.AdoptionStatus', 'Pending')
                ->count();

        $newAdoptionsToday = AdoptionApplication::join('pets', 'adoption_applications.PetID', '=', 'pets.PetID')
                ->where('pets.ShelterID', $userId)
                ->where('adoption_applications.AdoptionStatus', 'Pending')
                ->whereDate('adoption_applications.created_at', Carbon::today())
                ->count();

        $recentCancelledAdoptions = AdoptionApplication::join('pets', 'adoption_applications.PetID', '=', 'pets.PetID')
                ->where('pets.ShelterID', $userId)
                ->where('adoption_applications.AdoptionStatus', 'Cancelled')
                ->whereNotNull('adoption_applications.CancellationReason')
                ->whereDate('adoption_applications.updated_at', '>=', Carbon::now()->subDays(7))
                ->count();

        $petHealthRecords = HealthRecord::join('pets', 'health_records.PetID', '=', 'pets.PetID')
                ->where('pets.ShelterID', $userId)
                ->orderBy('health_records.LastCheckupDate', 'desc')
                ->select('health_records.*', 'pets.PetName')
                ->take(3)
                ->get();

        $petNeedingAttention = HealthRecord::where('HealthRemarks', '!=', 'Healthy')
                ->whereIn('PetID', function ($query) use ($userId) {
                    $query->select('PetID')->from('pets')->where('ShelterID', $userId);
                })
                ->selectRaw('COUNT(DISTINCT PetID) as pet_count')
                ->value('pet_count');

        $unreadMessages = Message::where('ReceiverID', $userId)
                ->where('is_read', false)
                ->count();

        $latestMessage = Message::where('ReceiverID', $userId)
                ->latest()
                ->with('sender')
                ->first();

        $nextAppointment = Appointment::join('pets', 'appointments.PetID', '=', 'pets.PetID')
                ->join('doctors', 'appointments.DoctorID', '=', 'doctors.DoctorID')
                ->join('users', 'appointments.AdopterID', '=', 'users.UserID')
                ->where('AppointmentDate', '>=', Carbon::now())
                ->where('is_cancelled', false)
                ->orderBy('AppointmentDate')
                ->select('appointments.*', 'doctors.DoctorName as doctor_name', 'users.name as adopter_name', 'pets.PetName')
                ->first();

        $publishedNotifications = Notification::count();

        $recentNotifications = Notification::orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        $resourceCount = PetResource::count();

        $popularResources = PetResource::orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        return view('ShelterStaff.dashboard', compact(
                        'petCount',
                        'availablePets',
                        'pendingAdoptions',
                        'newAdoptionsToday',
                        'petHealthRecords',
                        'petNeedingAttention',
                        'unreadMessages',
                        'latestMessage',
                        'nextAppointment',
                        'publishedNotifications',
                        'recentNotifications',
                        'resourceCount',
                        'popularResources',
                        'recentCancelledAdoptions'
                ));
    }

    public function showProfile() {
        $user = Auth::user();
        $profile = $user->shelterProfile;

        return view('ShelterStaff.profile', compact('user', 'profile'));
    }

    public function adoptionRequests(Request $request) {
        $shelterStaffID = Auth::id();

        $query = AdoptionApplication::whereHas('pet', function ($query) use ($shelterStaffID) {
                    $query->where('ShelterID', $shelterStaffID);
                })
                ->with(['pet', 'adopter', 'photos'])
                ->select('*'); 
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pet', function ($q2) use ($search) {
                            $q2->where('PetName', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('adopter', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                        });
            });
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('AdoptionStatus', $request->status);
        }

        $adoptions = $query->orderBy('ApplicationDate', 'desc')
                ->paginate(10)
                ->withQueryString(); // This preserves pagination with filters applied

        return view('ShelterStaff.adoption.adoption_requests', compact('adoptions'));
    }

    public function reviewAdoption($ApplicationID) {
        $adoption = AdoptionApplication::with(['adopter', 'pet'])
                ->where('ApplicationID', $ApplicationID)
                ->firstOrFail();

        if ($adoption->pet->ShelterID != Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('ShelterStaff.adoption.adoption_review', compact('adoption'));
    }

    public function approveAdoption($ApplicationID) {
        $adoption = AdoptionApplication::findOrFail($ApplicationID);

        if ($adoption->pet->ShelterID != Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $adoption->update(['AdoptionStatus' => 'Approved']);

        Pet::where('PetID', $adoption->PetID)->update([
            'AdopterID' => $adoption->AdopterID,
            'AdoptionStatus' => 'Not Available' 
        ]);

        return redirect()->route('shelter.adoptions')->with('success', 'Adoption approved successfully!');
    }

    public function rejectAdoption(Request $request, $ApplicationID) {
        $adoption = AdoptionApplication::findOrFail($ApplicationID);

        if ($adoption->pet->ShelterID != Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($adoption->ResubmissionCount >= 3) {
            return redirect()->route('shelter.adoptions')->with('error', 'This adopter has reached the maximum number of resubmissions.');
        }

        $request->validate([
            'staffNote' => 'required|string|min:10'
                ], [
            'staffNote.required' => 'You must provide a reason for rejection.',
            'staffNote.min' => 'The rejection reason must be at least 10 characters.'
        ]);

        $staffNote = $request->staffNote;

        $adoption->update([
            'AdoptionStatus' => 'Rejected',
            'LastRejectionDate' => now(),
            'StaffNotes' => $staffNote,
            'ResubmissionCount' => $adoption->ResubmissionCount + 1
        ]);

        $user = User::findOrFail($adoption->AdopterID);
        $petName = $adoption->pet->PetName;

        $canResubmit = $adoption->ResubmissionCount < 3;
        $resubmitDate = \Carbon\Carbon::parse($adoption->LastRejectionDate)->addDays(7)->format('d/m/Y');

        $subject = "Adoption Application Rejected";
        $message = "Your adoption application for {$petName} has been rejected. Reason: {$staffNote}";
        if ($canResubmit) {
            $message .= " You can resubmit your application with improvements after {$resubmitDate} (7 days from now).";
        } else {
            $message .= " You have reached the maximum number of resubmission attempts.";
        }

        $notificationData = [
            'pet_id' => $adoption->PetID,
            'pet_name' => $petName,
            'application_id' => $adoption->ApplicationID,
            'rejection_reason' => $staffNote,
            'can_resubmit' => $canResubmit,
            'resubmit_date' => $canResubmit ? $resubmitDate : null,
            'attempts_remaining' => $canResubmit ? 3 - $adoption->ResubmissionCount : 0
        ];

        Notification::create([
            'UserID' => $adoption->AdopterID,
            'type' => 'adoption_rejection',
            'message' => $subject . ': ' . $petName,
            'data' => $notificationData,
            'related_id' => $adoption->ApplicationID
        ]);
        Mail::to($user->email)->send(new AdoptionRejectedMail($adoption,
                        $staffNote,
                        $canResubmit,
                        $resubmitDate));

        return redirect()->route('shelter.adoptions')->with('success', 'Adoption rejected and notification sent to adopter.');
    }

    public function petHealthRecords() {
        $shelterStaffID = Auth::id();

        $healthRecords = HealthRecord::whereHas('pet', function ($query) use ($shelterStaffID) {
                    $query->where('ShelterID', $shelterStaffID);
                })->with('pet')->get();

        $latestRecords = $healthRecords->unique('PetID');

        return view('ShelterStaff.health_records.index', compact('healthRecords', 'latestRecords'));
    }

    public function viewHealthRecord($petId) {
        logger("Received Pet ID: " . $petId); 

        $shelterStaffID = Auth::id();

        $pet = Pet::where('PetID', $petId)
                ->where('ShelterID', $shelterStaffID)
                ->first();

        if (!$pet) {
            logger("Pet not found for ID: " . $petId);
            return redirect()->route('shelter.health')->with('error', 'Pet not found or unauthorized access.');
        }

        $healthRecords = HealthRecord::where('PetID', $petId)
                ->with('images')
                ->orderBy('LastCheckupDate', 'desc')
                ->get();

        return view('ShelterStaff.health_records.show', compact('pet', 'healthRecords'));
    }
    
    public function createHealthRecord() {
        $shelterStaffID = Auth::id();
        $pets = Pet::where('ShelterID', $shelterStaffID)->get();

        return view('ShelterStaff.health_records.add', compact('pets'));
    }

    public function storeHealthRecord(Request $request) {
        $shelterStaffID = Auth::id();

        $request->validate([
            'PetID' => 'required|exists:pets,PetID',
            'HealthRemarks' => 'nullable|string|max:500',
            'LastCheckupDate' => 'required|date|before_or_equal:today',
            'Diagnosis' => 'nullable|string|max:500',
            'Medicine' => 'nullable|string|max:500',
            'Allergy' => 'required|boolean',
            'AllergyDetails' => 'nullable|required_if:Allergy,1|string|max:255',
            'HealthRecordImages.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $pet = Pet::where('PetID', $request->PetID)
                ->where('ShelterID', $shelterStaffID)
                ->first();

        if (!$pet) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $pet->update([
            'Allergy' => (bool) $request->Allergy,
            'AllergyDetails' => $request->Allergy == 1 ? $request->AllergyDetails : null
        ]);

        $vaccinationStatus = $pet->VaccinationStatus;
        $sterilization = $pet->Neutering ? 'Neutered' : 'Not Neutered';
        $allergyDetails = $pet->Allergy ? $pet->AllergyDetails : null;

        $healthRecord = HealthRecord::create([
            'PetID' => $request->PetID,
            'VaccinationStatus' => $vaccinationStatus,
            'HealthRemarks' => $request->HealthRemarks,
            'Sterilization' => $sterilization,
            'LastCheckupDate' => $request->LastCheckupDate,
            'Diagnosis' => $request->Diagnosis,
            'Medicine' => $request->Medicine
        ]);

        if ($healthRecord) {
            if ($request->hasFile('HealthRecordImages')) {
                foreach ($request->file('HealthRecordImages') as $file) {
                    $imagePath = $file->store('uploads/health_records', 'public');

                    PetHealthRecordImage::create([
                        'RecordID' => $healthRecord->RecordID,
                        'ImagePath' => $imagePath,
                    ]);
                }
            }
        }

        return redirect()->route('shelter.health')->with('success', 'Health record added successfully!');
    }

    public function editHealthRecord($recordId) {
        $shelterStaffID = Auth::id();

        $healthRecord = HealthRecord::where('RecordID', $recordId)
                ->whereHas('pet', function ($query) use ($shelterStaffID) {
                    $query->where('ShelterID', $shelterStaffID);
                })
                ->with('pet', 'images') 
                ->first();

        if (!$healthRecord) {
            return redirect()->route('shelter.health')->with('error', 'Health record not found or unauthorized access.');
        }

        return view('ShelterStaff.health_records.edit', compact('healthRecord'));
    }

    public function updateHealthRecord(Request $request, $recordId) {
        try {
            $shelterStaffID = Auth::id();
            $healthRecord = HealthRecord::where('RecordID', $recordId)
                    ->whereHas('pet', function ($query) use ($shelterStaffID) {
                        $query->where('ShelterID', $shelterStaffID);
                    })
                    ->first();

            if (!$healthRecord) {
                \Log::warning('Health record not found or unauthorized. Record ID: ' . $recordId . ', Staff ID: ' . $shelterStaffID);
                return redirect()->route('shelter.health')->with('error', 'Health record not found or unauthorized action.');
            }

            \Log::info('Form data received:', $request->all());
            $validated = $request->validate([
                'HealthRemarks' => 'nullable|string|max:500',
                'LastCheckupDate' => 'required|date|before_or_equal:today',
                'Diagnosis' => 'nullable|string|max:500',
                'Medicine' => 'nullable|string|max:500',
                'Allergy' => 'required|boolean',
                'AllergyDetails' => 'nullable|string|max:255',
                'HealthRecordImages.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            \Log::info('Validation passed. Processing update for record ID: ' . $recordId);

            $pet = $healthRecord->pet;
            $pet->update([
                'Allergy' => $request->Allergy,
                'AllergyDetails' => $request->has('Allergy') && $request->Allergy == 1 ? $request->AllergyDetails : null
            ]);

            \Log::info('Pet allergy info updated for Pet ID: ' . $pet->PetID);

            $vaccinationStatus = $pet->VaccinationStatus;
            $sterilization = $pet->Neutering ? 'Neutered' : 'Not Neutered';

            $healthRecord->update([
                'VaccinationStatus' => $vaccinationStatus,
                'HealthRemarks' => $request->HealthRemarks,
                'Sterilization' => $sterilization,
                'LastCheckupDate' => $request->LastCheckupDate,
                'Diagnosis' => $request->Diagnosis,
                'Medicine' => $request->Medicine
            ]);

            \Log::info('Health record data updated successfully for Record ID: ' . $recordId);

            if ($request->hasFile('HealthRecordImages')) {
                \Log::info('Processing ' . count($request->file('HealthRecordImages')) . ' image uploads');
                foreach ($request->file('HealthRecordImages') as $file) {
                    $imagePath = $file->store('uploads/health_records', 'public');
                    PetHealthRecordImage::create([
                        'RecordID' => $healthRecord->RecordID,
                        'ImagePath' => $imagePath,
                    ]);

                    \Log::info('Image saved: ' . $imagePath);
                }
            }
            \Log::info('Health record update completed successfully');

            return redirect()->route('shelter.health.view', $healthRecord->pet->PetID)
                            ->with('success', 'Health record updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error while updating health record: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating health record: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                            ->with('error', 'Error updating record: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function deleteImage($id) {
        try {
            $image = PetHealthRecordImage::find($id);
            if (!$image) {
                return redirect()->back()->with('error', 'Image not found.');
            }

            $recordId = $image->RecordID;

            if (file_exists(public_path($image->ImagePath))) {
                unlink(public_path($image->ImagePath));
            }

            if (Storage::exists('public/' . $image->ImagePath)) {
                Storage::delete('public/' . $image->ImagePath);
            }

            $image->delete();

            return redirect()->back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }

    public function messageCenter() {
        $messages = Message::orderBy('created_at', 'desc')->get();
        return view('ShelterStaff.message_center', compact('messages'));
    }
}

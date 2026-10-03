<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentConfirmationMail;
use App\Mail\EmergencyOverrideMail;
use App\Models\AdopterProfile;

class AppointmentController extends Controller {

    public function index(Request $request) {
        $query = Appointment::with(['adopter', 'pet', 'doctor']);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('adopter', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('pet', function ($q) use ($search) {
                            $q->where('PetName', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('doctor', function ($q) use ($search) {
                            $q->where('DoctorName', 'like', '%' . $search . '%');
                        })
                        ->orWhere('Purpose', 'like', '%' . $search . '%');
            });
        }
        if ($filter = $request->input('filter')) {
            switch ($filter) {
                case 'Upcoming':
                    $query->where('AppointmentDate', '>=', now());
                    break;
                case 'Shelter Booked':
                    $query->whereNull('AdopterID');
                    break;
                case 'Adopter Booked':
                    $query->whereNotNull('AdopterID');
                    break;
                case 'Expired':
                    $query->where('AppointmentDate', '<', now());
                    break;
                case 'Cancelled':
                    $query->where('Status', 'Cancelled');
                    break;
            }
        }
        if ($date = $request->input('date')) {
            $query->whereDate('AppointmentDate', $date);
        }
        $appointments = $query->orderBy('AppointmentDate', 'asc')->paginate(5);
        return view('shelterstaff.appointments.index', compact('appointments'));
    }

    public function create() {
        $user = Auth::user();
        $pets = Pet::where('ShelterID', $user->UserID)
                ->where('AdoptionStatus', 'available')
                ->get();
        $doctors = Doctor::all();

        return view('shelterstaff.appointments.create', compact('pets', 'doctors'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'doctor_id' => 'required|exists:doctors,DoctorID',
            'timeslot_id' => 'required|exists:doctor_schedules,ScheduleID',
            'pet_id' => 'required|exists:pets,PetID',
            'purpose' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $userID = $user->UserID;
        $pet = Pet::where('PetID', $request->pet_id)
                ->where('ShelterID', $userID)
                ->first();

        if (!$pet) {
            return redirect()->back()->withErrors([
                        'pet_id' => 'You can only book an appointment for pets under your management.'
            ]);
        }

        $doctorSchedule = DoctorSchedule::where('ScheduleID', $request->timeslot_id)
                ->where('DoctorID', $request->doctor_id)
                ->first();

        if (!$doctorSchedule) {
            return redirect()->back()->withErrors(['timeslot_id' => 'Invalid timeslot selection.']);
        }

        if ($doctorSchedule->IsBooked) {
            return redirect()->back()->withErrors(['timeslot_id' => 'This timeslot has already been booked. Please select another.']);
        }

        $existingAppointment = Appointment::where('PetID', $request->pet_id)
                ->where('AppointmentDate', $request->date)
                ->where('Status', '!=', 'Cancelled')
                ->whereHas('timeslot', function ($query) use ($request) {
                    $selectedTimeslot = DoctorSchedule::where('ScheduleID', $request->timeslot_id)->first();
                    if ($selectedTimeslot) {
                        $query->where('Timeslot', $selectedTimeslot->Timeslot);
                    }
                })
                ->first();
        if ($existingAppointment) {
            return redirect()->back()->withErrors([
                        'pet_id' => 'This pet already has an appointment scheduled at the selected time.'
                    ])->withInput();
        }

        Log::info('Creating appointment for shelter staff', [
            'shelter_staff_id' => $userID, // Update this to use UserID
            'doctor_id' => $request->doctor_id,
            'timeslot_id' => $request->timeslot_id,
            'pet_id' => $request->pet_id,
            'date' => $request->date,
            'purpose' => $request->purpose,
        ]);

        $appointment = Appointment::create([
            'AppointmentDate' => $request->date,
            'Purpose' => $request->purpose,
            'Status' => 'Confirmed',
            'DoctorID' => $request->doctor_id,
            'ShelterStaffID' => $userID,
            'PetID' => $request->pet_id,
            'timeslot_id' => $request->timeslot_id,
            'created_by_user_id' => $userID,
            'last_modified_by_user_id' => $userID,
        ]);

        if (!$appointment) {
            Log::error('Failed to create appointment for shelter staff.');
            return redirect()->back()->withErrors(['error' => 'Failed to create the appointment. Please try again.']);
        }

        $doctorSchedule->update(['IsBooked' => true]);
        try {
            Mail::to($user->email)->send(new \App\Mail\AppointmentConfirmationMail($appointment));
            Log::info("Confirmation email sent for appointment ID: " . $appointment->AppointmentID);
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', ['error' => $e->getMessage()]);
        }

        return redirect()->route('shelter.appointment.success', $appointment->AppointmentID);
    }

    public function edit($appointmentID) {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('shelter.login')
                            ->withErrors(['error' => 'You must be logged in to access this page.']);
        }
        if ($user->role !== 'shelter_staff') {
            return redirect()->route('shelter.login')
                            ->withErrors(['error' => 'You are not authorized to access this page.']);
        }
        $appointment = Appointment::with(['pet', 'doctor', 'timeslot'])->findOrFail($appointmentID);

        if (!$appointment->pet) {
            return redirect()->back()
                            ->withErrors(['error' => 'Pet not found for this appointment.']);
        }
        if ($appointment->pet->ShelterID != $user->UserID) {
            return redirect()->back()
                            ->withErrors(['error' => 'You are not authorized to edit this appointment.']);
        }
        $doctors = Doctor::all();
        $timeslots = DoctorSchedule::where('DoctorID', $appointment->DoctorID)
                ->where('AvailableDate', $appointment->AppointmentDate)
                ->where(function ($query) use ($appointment) {
                    $query->where('IsBooked', false)
                            ->orWhere('ScheduleID', $appointment->timeslot_id);
                })
                ->pluck('Timeslot', 'ScheduleID');

        return view('shelterstaff.appointments.reschedule', compact('appointment', 'doctors', 'timeslots'));
    }

    public function reschedule(Request $request, $id) {
        Log::info("Shelter staff rescheduling appointment ID: " . $id);
        $user = Auth::user();
        $userID = $user->UserID;

        $appointment = Appointment::with('pet')->findOrFail($id);
        if ($appointment->pet->ShelterID != $userID) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to reschedule this appointment.']);
        }

        if ($appointment->is_rescheduled) {
            return redirect()->back()->withErrors(['error' => 'You can only reschedule once.']);
        }

        $request->validate([
            'date' => 'required|date|after:today',
            'doctor_id' => 'required|exists:doctors,DoctorID',
            'timeslot_id' => 'required|exists:doctor_schedules,ScheduleID',
            'purpose' => 'required|string|max:255',
        ]);

        $doctorSchedule = DoctorSchedule::where('ScheduleID', $request->timeslot_id)
                ->where('DoctorID', $request->doctor_id)
                ->first();
        if (!$doctorSchedule || $doctorSchedule->IsBooked) {
            return redirect()->back()->withErrors(['timeslot_id' => 'The selected timeslot is no longer available.']);
        }
        $existingAppointment = Appointment::where('PetID', $appointment->PetID)
                ->where('AppointmentDate', $request->date)
                ->where('Status', '!=', 'Cancelled')
                ->where('AppointmentID', '!=', $id) // Exclude the current appointment
                ->whereHas('timeslot', function ($query) use ($request) {
                    $selectedTimeslot = DoctorSchedule::where('ScheduleID', $request->timeslot_id)->first();
                    if ($selectedTimeslot) {
                        $query->where('Timeslot', $selectedTimeslot->Timeslot);
                    }
                })
                ->first();
        if ($existingAppointment) {
            return redirect()->back()->withErrors([
                        'pet_id' => 'This pet already has an appointment scheduled at the selected time.'
                    ])->withInput();
        }
        $appointment->update([
            'AppointmentDate' => $request->date,
            'DoctorID' => $request->doctor_id,
            'timeslot_id' => $request->timeslot_id,
            'Purpose' => $request->purpose,
            'Status' => 'Rescheduled',
            'is_rescheduled' => true,
            'last_modified_by_user_id' => $userID,
        ]);

        $doctorSchedule->update(['IsBooked' => true]);
        try {
            Mail::to($user->email)->send(new \App\Mail\AppointmentRescheduleMail($appointment));
            Log::info("Reschedule email sent for appointment ID: {$id}");
        } catch (\Exception $e) {
            Log::error("Failed to send reschedule email for appointment ID: {$id}", ['error' => $e->getMessage()]);
        }
        return redirect()->route('shelter.appointments')->with('success', 'Appointment rescheduled successfully.');
    }

    public function cancel($id) {
        Log::info("Shelter staff attempting to cancel appointment ID: " . $id);
        $user = Auth::user();
        $userID = $user->UserID;
        $appointment = Appointment::with(['pet', 'timeslot'])->findOrFail($id);

        if ($appointment->Status === 'Cancelled' || $appointment->is_cancelled) {
            return redirect()->back()->withErrors([
                        'error' => 'This appointment has already been cancelled.'
            ]);
        }
        if ($appointment->pet->ShelterID != $userID) {
            return redirect()->back()->withErrors([
                        'error' => 'You are not authorized to cancel this appointment.'
            ]);
        }
        $appointment->update([
            'Status' => 'Cancelled',
            'is_cancelled' => true,
            'last_modified_by_user_id' => $userID
        ]);

        if ($appointment->timeslot_id) {
            $doctorSchedule = DoctorSchedule::find($appointment->timeslot_id);
            if ($doctorSchedule) {
                $doctorSchedule->update(['IsBooked' => false]);
                Log::info("Released timeslot ID: " . $appointment->timeslot_id);
            }
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\AppointmentCancellationMail($appointment));
            Log::info("Cancellation email sent for appointment ID: " . $appointment->AppointmentID);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email', ['error' => $e->getMessage()]);
        }

        return redirect()->route('shelter.appointments')
                        ->with('success', 'Appointment cancelled successfully.');
    }

    public function success($appointmentID) {
        $appointment = Appointment::with(['pet', 'doctor'])
                ->findOrFail($appointmentID);
        return view('shelterstaff.appointments.success', compact('appointment'));
    }

    public function getTimeSlots(Request $request) {
        Log::info('getTimeSlots for Shelter Staff!', [
            'doctor_id' => $request->doctor_id,
            'date' => $request->date
        ]);

        if (!$request->has('doctor_id') || !$request->has('date')) {
            return response()->json(['error' => 'Missing doctor_id or date'], 400);
        }
        $doctorID = $request->doctor_id;
        $formattedDate = $request->date;
        $currentTime = now()->format('H:i');
        $maxDate = Carbon::now();
        $workingDays = 0;
        while ($workingDays < 5) {
            $maxDate->addDay();
            if ($maxDate->isWeekday()) {
                $workingDays++;
            }
        }
        if (Carbon::parse($formattedDate)->gt($maxDate)) {
            return response()->json([
                        'error' => 'Appointments can only be scheduled within the next 5 working days.'
                            ], 400);
        }
        $query = DoctorSchedule::where('DoctorID', $doctorID)
                ->where('AvailableDate', $formattedDate)
                ->where('IsBooked', false);

        // If the selected date is today, filter out past times
        if ($formattedDate == now()->format('Y-m-d')) {
            $query->whereRaw("
            TIME_FORMAT(STR_TO_DATE(SUBSTRING_INDEX(Timeslot, ' - ', 1), '%h:%i %p'), '%H:%i') > ?
        ", [$currentTime]);
        }
        $timeslots = $query->pluck('Timeslot', 'ScheduleID');

        if ($timeslots->isEmpty()) {
            return response()->json([
                        'error' => 'No available timeslots. Appointment bookings are only valid for the next 5 working days.'
                            ], 404);
        }
        return response()->json($timeslots);
    }

    public function emergencyOverride($appointmentID) {
        $user = Auth::user();
        $userID = $user->UserID;

        $appointment = Appointment::with('adopter')->findOrFail($appointmentID);

        // Ensure that this emergency override applies only to adopter-booked appointments
        if (!$appointment->adopter) {
            return redirect()->back()->withErrors([
                        'error' => 'Emergency override is only applicable to adopter-booked appointments.'
            ]);
        }
        Log::info('Executing emergency override', [
            'appointment_id' => $appointmentID,
            'shelter_staff_id' => $userID,
        ]);
        $appointment->update([
            'Status' => 'Emergency Override',
            'last_modified_by_user_id' => $userID,
        ]);

        try {
            Mail::to($appointment->adopter->email)->send(new EmergencyOverrideMail($appointment));
            Log::info("Emergency override email sent for appointment ID: " . $appointment->AppointmentID);
        } catch (\Exception $e) {
            Log::error('Failed to send emergency override email', ['error' => $e->getMessage()]);
        }

        return redirect()->route('shelter.appointments')
                        ->with('success', 'Emergency override executed successfully.');
    }

    public function contactAdopter($appointmentID) {
        \Log::info("Attempting to contact adopter for appointment ID: " . $appointmentID);
        $appointment = Appointment::with('adopter')->findOrFail($appointmentID);
        if (!$appointment->adopter) {
            return redirect()->back()->withErrors([
                        'error' => 'This appointment is not booked by an adopter.'
            ]);
        }
        $receiverId = $appointment->adopter->UserID;

        return redirect()->route('messages.chat', ['receiverId' => $receiverId]);
    }
}

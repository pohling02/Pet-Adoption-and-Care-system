<?php

namespace App\Http\Controllers\Adopter;

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
use App\Models\AdopterProfile;

class AppointmentController extends Controller {

    public function index() {
        $user = Auth::user();
        $appointments = Auth::user()->appointments()
                ->with(['pet', 'doctor', 'timeslot'])
                ->get()
                ->map(function ($appointment) {
                    $appointmentDate = \Carbon\Carbon::parse($appointment->AppointmentDate);
                    if ($appointmentDate->isPast() && $appointment->Status != 'Cancelled') {
                        $appointment->Status = 'Expired';
                    }
                    return $appointment;
                });
        $profile = AdopterProfile::where('UserID', $user->UserID)->first();

        return view('Adopter.appointment.show', compact('user', 'appointments', 'profile'));
    }

    public function create() {
        $user = Auth::user();

        // Fetch adopted pets that have been approved
        $adoptedPets = $user->adoptedPets()
                ->where('adoption_applications.AdoptionStatus', 'Approved')
                ->with('pet')
                ->get();

        Log::info("Fetched Adopted Pets:", $adoptedPets->toArray());

        $doctors = Doctor::all();

        return view('Adopter.appointment.create', compact('doctors', 'adoptedPets'));
    }

    public function getTimeSlots(Request $request) {
        Log::info('getTimeSlots function was called!', [
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

        if ($formattedDate == now()->format('Y-m-d')) {
            Log::info('Filtering past times since today is selected', ['current_time' => $currentTime]);

            $query->whereRaw("
            TIME_FORMAT(STR_TO_DATE(SUBSTRING_INDEX(Timeslot, ' - ', 1), '%h:%i %p'), '%H:%i') > ?
        ", [$currentTime]);
        }

        if ($request->has('reschedule') && $request->reschedule == true) {
            $currentAppointment = Appointment::find($request->appointment_id);
            if ($currentAppointment) {
                $query->orWhere('ScheduleID', $currentAppointment->timeslot_id);
            }
        }

        $timeslots = $query->pluck('Timeslot', 'ScheduleID');

        Log::info('Timeslot Query Result:', [
            'doctor_id' => $doctorID,
            'date' => $formattedDate,
            'timeslots' => $timeslots
        ]);

        if ($timeslots->isEmpty()) {
            return response()->json([
                        'error' => 'No available timeslots. Appointment bookings '
                . 'are only valid for the next 5 working days.'
                            ], 404);
        }

        return response()->json($timeslots);
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'doctor_id' => 'required|exists:doctors,DoctorID',
            'timeslot_id' => 'required|exists:doctor_schedules,ScheduleID',
            'pet_id' => 'required|exists:pets,PetID',
            'purpose' => 'required|string|max:255',
        ]);

        $doctorSchedule = DoctorSchedule::where('ScheduleID', $request->timeslot_id)
                ->where('DoctorID', $request->doctor_id)
                ->first();

        if (!$doctorSchedule) {
            return redirect()->back()->withErrors(['timeslot_id' => 'Invalid timeslot selection.']);
        }

        if ($doctorSchedule->IsBooked) {
            return redirect()->back()->withErrors(['timeslot_id' => 'This timeslot has already been booked. '
                . 'Please select another.']);
        }

        if ($doctorSchedule->DoctorID != $request->doctor_id) {
            return redirect()->back()->withErrors(['doctor_id' => 'Selected timeslot does not belong to this doctor.']);
        }

        $pet = Auth::user()->adoptedPets()->whereHas('pet', function ($query) use ($request) {
                    $query->where('PetID', $request->pet_id);
                })->first();

        if (!$pet) {
            return redirect()->back()->withErrors(['pet_id' => 'You can only book an appointment for adopted pets.']);
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
        Log::info('Creating appointment', [
            'adopter_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'timeslot_id' => $request->timeslot_id,
            'pet_id' => $request->pet_id,
            'date' => $request->date,
            'purpose' => $request->purpose
        ]);
        $appointment = Appointment::create([
            'AppointmentDate' => $request->date,
            'Purpose' => $request->purpose,
            'Status' => 'Confirmed',
            'DoctorID' => $request->doctor_id,
            'AdopterID' => Auth::id(),
            'PetID' => $request->pet_id,
            'timeslot_id' => $request->timeslot_id,
        ]);
        if (!$appointment) {
            Log::error('❌ Failed to create appointment.');
            return redirect()->back()->withErrors(['error' => 'Failed to create the appointment. Please try again.']);
        }
        $doctorSchedule->update(['IsBooked' => true]);
        try {
            if (!isset($appointment) || empty($appointment)) {
                throw new \Exception('❌ Appointment variable is undefined.');
            }
            Log::info("✅ Preparing to send email with appointment ID: " . $appointment->AppointmentID);
            Mail::to(Auth::user()->email)->send(new \App\Mail\AppointmentConfirmationMail($appointment));
            Log::info("✅ Email sent successfully.");
        } catch (\Exception $e) {
            Log::error('❌ Failed to send email', ['error' => $e->getMessage()]);
        }

        return redirect()->route('adopter.appointment.success', ['appointment' => $appointment->AppointmentID])
                        ->with('success', 'Appointment booked successfully!');
    }

    public function show($id) {
        $appointment = Appointment::with(['pet', 'doctor'])->findOrFail($id);
        return view('Adopter.appointment.details', compact('appointment'));
    }

    public function edit($appointmentID) {
        Log::info("Editing appointment ID: " . $appointmentID);

        $appointment = Appointment::with(['pet', 'doctor', 'timeslot'])->findOrFail($appointmentID);

        $doctors = Doctor::all();

        $timeslots = DoctorSchedule::where('DoctorID', $appointment->DoctorID)
                ->where('AvailableDate', $appointment->AppointmentDate)
                ->where(function ($query) use ($appointment) {
                    $query->where('IsBooked', false)
                            ->orWhere('ScheduleID', $appointment->timeslot_id);
                })
                ->pluck('Timeslot', 'ScheduleID');

        Log::info('Available timeslots:', ['timeslots' => $timeslots]);

        return view('Adopter.appointment.reschedule', compact('appointment', 'doctors', 'timeslots'));
    }

    public function reschedule(Request $request, $id) {
        Log::info("Rescheduling appointment ID: " . $id);
        $appointment = Appointment::findOrFail($id);

        if ($appointment->is_rescheduled) {
            return redirect()->back()->withErrors('You can only reschedule once.');
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

        $oldTimeslot = DoctorSchedule::where('ScheduleID', $appointment->timeslot_id)->first();
        if ($oldTimeslot) {
            $oldTimeslot->update(['IsBooked' => false]);
        }
        $appointment->update([
            'AppointmentDate' => $request->date,
            'DoctorID' => $request->doctor_id,
            'timeslot_id' => $request->timeslot_id,
            'Purpose' => $request->purpose,
            'Status' => 'Rescheduled',
            'is_rescheduled' => true,
        ]);

        Log::info("✅ Appointment ID {$id} updated with status: " . $appointment->Status);

        $doctorSchedule->update(['IsBooked' => true]);

        try {
            Mail::to(Auth::user()->email)->send(new \App\Mail\AppointmentRescheduleMail($appointment));
            Log::info("Reschedule email sent for appointment ID: {$id}");
        } catch (\Exception $e) {
            Log::error("Failed to send reschedule email for appointment ID: {$id}", ['error' => $e->getMessage()]);
        }

        return redirect()->route('adopter.appointments')->with('success', 'Appointment rescheduled successfully.');
    }
    
    
    public function cancel($id) {
        Log::info("Attempting to cancel appointment ID: " . $id);
        $appointment = Appointment::findOrFail($id);
        if ($appointment->is_cancelled) {
            Log::warning("Appointment ID {$id} has already been cancelled.");
            return redirect()->back()->withErrors('This appointment has already been cancelled.');
        }

        if (\Carbon\Carbon::parse($appointment->AppointmentDate)->isPast()) {
            Log::warning("Attempted to cancel past appointment ID: {$id}.");
            return redirect()->back()->withErrors('You cannot cancel past appointments.');
        }
        if ($appointment->timeslot_id) {
            $doctorSchedule = DoctorSchedule::where('ScheduleID', $appointment->timeslot_id)->first();
            if ($doctorSchedule) {
                $doctorSchedule->update(['IsBooked' => false]);
                Log::info("Released timeslot ID: " . $appointment->timeslot_id);
            }
        }
        $appointment->update([
            'Status' => 'Cancelled',
            'is_cancelled' => true,
        ]);
        Log::info("Appointment ID {$id} has been successfully cancelled.");

        try {
            Mail::to(Auth::user()->email)->send(new \App\Mail\AppointmentCancelMail($appointment));
            Log::info("Cancellation email sent for appointment ID: {$id}");
        } catch (\Exception $e) {
            Log::error("Failed to send cancellation email for appointment ID: {$id}", ['error' => $e->getMessage()]);
        }
        return redirect()->back()->with('success', 'Appointment successfully cancelled.');
    }
}

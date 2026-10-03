<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentRescheduleMail extends Mailable {

    use Queueable,
        SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment) {
        $this->appointment = $appointment;
    }

    public function build() {
        return $this->subject('Your Appointment Has Been Rescheduled')
                        ->view('adopter.appointment.reschedule_email')
                        ->with([
                            'appointment' => $this->appointment,
                            'user' => $this->appointment->adopter,
                            'doctors' => \App\Models\Doctor::all(), // ✅ Ensure $doctors is passed
                            'timeslots' => \App\Models\DoctorSchedule::where('DoctorID', $this->appointment->DoctorID)
                            ->where('AvailableDate', $this->appointment->AppointmentDate)
                            ->where(function ($query) {
                                $query->where('IsBooked', false)
                                        ->orWhere('ScheduleID', $this->appointment->timeslot_id);
                            })
                            ->pluck('Timeslot', 'ScheduleID') // ✅ Ensure $timeslots is passed
        ]);
    }
}

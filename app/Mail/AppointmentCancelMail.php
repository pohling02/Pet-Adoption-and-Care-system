<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\User; // Ensure User model is available

class AppointmentCancelMail extends Mailable {

    use Queueable, SerializesModels;

    public $appointment;
    public $user;

    public function __construct(Appointment $appointment) {
        $this->appointment = $appointment;
        $this->user = $appointment->adopter; // ✅ Ensure adopter is assigned correctly
    }

    public function build() {
        return $this->subject('Your Appointment Has Been Cancelled')
                    ->view('adopter.appointment.cancel_email')
                    ->with([
                        'appointment' => $this->appointment,
                        'user' => $this->user, // ✅ Ensure user variable is correctly passed
                    ]);
    }
}

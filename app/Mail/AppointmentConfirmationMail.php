<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment; 

class AppointmentConfirmationMail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * Create a new message instance.
     */
    public $appointment;

    public function __construct(Appointment $appointment) {
        $this->appointment = $appointment;
    }

    public function build() {
        return $this->subject('Appointment Confirmation')
                        ->view('adopter.appointment.confirmation_email')
                        ->with(['appointment' => $this->appointment]);
    }
}

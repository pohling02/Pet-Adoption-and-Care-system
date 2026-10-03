<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct() {
        //
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }

    public function build() {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                        ->subject('Test Email from Laravel')
                        ->view('emails.test_email');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification {

    use Queueable;

    public $resetUrl;

    public function __construct($resetUrl) {
        $this->resetUrl = $resetUrl;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
                        ->subject('Reset Your Password')
                        ->line('Click the button below to reset your password.')
                        ->action('Reset Password', $this->resetUrl)
                        ->line('If you did not request a password reset, ignore this email.');
    }
}

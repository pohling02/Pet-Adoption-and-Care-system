<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\AdoptionApplication;
use Carbon\Carbon;


class AdoptionRejectedMail extends Mailable {

    use Queueable,
        SerializesModels;

    public $adoption;
    public $rejectionReason;
    public $canResubmit;
    public $daysUntilResubmission;
    public $resubmitDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AdoptionApplication $adoption, $rejectionReason) {
        $this->adoption = $adoption;
        $this->rejectionReason = $rejectionReason;
        $this->canResubmit = $adoption->ResubmissionCount < 3;

        // Calculate days until they can resubmit
        if ($this->canResubmit && $adoption->LastRejectionDate) {
            $rejectionDate = Carbon::parse($adoption->LastRejectionDate);
            $resubmitDate = $rejectionDate->copy()->addDays(7);

            $this->daysUntilResubmission = max(0, $resubmitDate->diffInDays(now()));
            $this->resubmitDate = $resubmitDate->format('d/m/Y');
        } else {
            $this->daysUntilResubmission = 7;
            $this->resubmitDate = null;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Your Adoption Application for ' . $this->adoption->pet->PetName . ' Was Not Approved')
                        ->view('emails.adoption_rejected');
    }
}

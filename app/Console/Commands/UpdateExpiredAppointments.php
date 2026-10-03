<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class UpdateExpiredAppointments extends Command {

    protected $signature = 'appointments:expire';
    protected $description = 'Update expired appointments automatically';

    public function handle() {
        $now = Carbon::now();

        // Find all appointments that are still "Confirmed" but past their date
        $expiredAppointments = Appointment::where('Status', 'Confirmed')
                ->where('AppointmentDate', '<', $now)
                ->update(['Status' => 'Expired']);

        $this->info($expiredAppointments . ' appointments marked as expired.');
    }
}

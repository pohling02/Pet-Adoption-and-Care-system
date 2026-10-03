<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $table = 'doctor_schedules'; // Specify table name
    protected $primaryKey = 'ScheduleID'; // Set primary key

    protected $fillable = [
        'DoctorID',
        'AvailableDate',
        'Timeslot',
        'IsBooked',
    ];

    // Relationship: Each schedule belongs to one doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'DoctorID', 'DoctorID');
    }
}

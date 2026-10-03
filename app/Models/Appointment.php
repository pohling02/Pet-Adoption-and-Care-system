<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {

    use HasFactory;

    protected $table = 'appointments';
    protected $primaryKey = 'AppointmentID';
    protected $fillable = [
        'AppointmentDate',
        'Purpose',
        'Status',
        'is_rescheduled',
        'is_cancelled',
        'AdopterID',
        'ShelterStaffID',
        'PetID',
        'DoctorID', 
        'timeslot_id',
        'created_by_user_id', 
        'last_modified_by_user_id'
    ];

    // Relationship: An appointment belongs to an adopter (user)
    public function adopter() {
        return $this->belongsTo(User::class, 'AdopterID', 'UserID');
    }

    // Relationship: An appointment is for a pet
    public function pet() {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
    }

    // Relationship: An appointment is assigned to a doctor
    public function doctor() {
        return $this->belongsTo(Doctor::class, 'DoctorID', 'DoctorID');
    }

    public function timeslot() {
        return $this->belongsTo(DoctorSchedule::class, 'timeslot_id', 'ScheduleID');
    }
}

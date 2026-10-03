<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model {
    use HasFactory;

    protected $table = 'doctors';
    protected $primaryKey = 'DoctorID'; // Ensure correct primary key
    protected $fillable = ['DoctorName', 'DoctorEmail', 'Specialization'];

    // Relationship: A doctor has many appointments
    public function appointments() {
        return $this->hasMany(Appointment::class, 'DoctorID', 'DoctorID');
    }

    // Relationship: A doctor has many schedules
    public function schedules() {
        return $this->hasMany(DoctorSchedule::class, 'DoctorID', 'DoctorID');
    }
}

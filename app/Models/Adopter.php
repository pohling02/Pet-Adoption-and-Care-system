<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adopter extends Model
{
    use HasFactory;
    
    public function adoptionApplications()
    {
        return $this->hasMany(AdoptionApplication::class, 'AdopterID', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'AdopterID', 'id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'AdopterID', 'id');
    }
    
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}

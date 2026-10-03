<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShelterStaff extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function pets()
    {
        return $this->hasMany(Pet::class, 'ShelterStaffID', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'ShelterStaffID', 'id');
    }
    
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}

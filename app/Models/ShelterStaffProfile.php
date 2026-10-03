<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShelterStaffProfile extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'shelter_staff_profiles';
    protected $primaryKey = 'ProfileID';

    protected $fillable = [
        'UserID',
        'phone_number',
        'shelter_name',
        'shelter_address',
        'position',
        'gender',
        'bio',
        'business_license',  
        'profile_picture',   
        'admin_remarks',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}


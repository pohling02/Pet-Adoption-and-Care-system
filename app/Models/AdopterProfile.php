<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdopterProfile extends Model
{
    use HasFactory;

    protected $table = 'adopter_profiles';
    protected $primaryKey = 'ProfileID'; // Profile's primary key

    protected $fillable = [
        'UserID',
        'phone_number',
        'address',
        'occupation',
        'pet_preference',
        'preferred_location',
        'profile_picture',
        'bio',
        'gender',
        'activity_level',
        'home_type',
        'other_pets',
        'allergies',
        'hours_per_day',
        'personality_preference'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID'); // Ensure correct reference
    }

    public function adoptedPets()
    {
        return $this->hasMany(Pet::class, 'AdopterID', 'UserID');
    }
}

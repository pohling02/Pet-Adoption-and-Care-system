<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model {

    use HasFactory, SoftDeletes;

    protected $table = 'pets';
    protected $primaryKey = 'PetID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'PetCode',
        'PetName',
        'Species',
        'Breed',
        'ManualBreed',
        'Color',
        'Gender',
        'DateOfBirth',
        'Personality',
        'Background',
        'SpecialNeed',
        'AdoptionStatus',
        'CurrentLocation',
        'CurrentAddress',
        'VaccinationStatus',
        'HealthCondition',
        'Allergy',
        'AllergyDetails',
        'Neutering',
        'EnergyLevel',
        'BarkingLevel',
        'SheddingLevel',
        'Ideal_Environment',
        'Appetite',
        'Friendliness',
        'Adaptability',
        'ShelterID',
        'AdopterID'
    ];
    
    protected $dates = ['deleted_at'];

    protected static function booted() {
        static::creating(function ($pet) {
            if (empty($pet->PetCode)) {
                if (strtolower($pet->Species) === 'dog') {
                    $prefix = 'D';
                } elseif (strtolower($pet->Species) === 'cat') {
                    $prefix = 'C';
                } else {
                    $prefix = 'P'; 
                }
                $countWithPrefix = Pet::where('PetCode', 'LIKE', $prefix . '%')->count() + 1;

                $pet->PetCode = $prefix . str_pad($countWithPrefix, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function images() {
        return $this->hasMany(PetImage::class, 'PetID', 'PetID');
    }

    public function adoptionApplications() {
        return $this->hasMany(AdoptionApplication::class, 'PetID', 'PetID');
    }

    public function healthRecords() {
        return $this->hasMany(HealthRecord::class, 'PetID', 'PetID');
    }

    public function shelter() {
        return $this->belongsTo(User::class, 'ShelterID', 'UserID');
    }

    public function adopter() {
        return $this->belongsTo(User::class, 'AdopterID', 'UserID');
    }

    public function appointments() {
        return $this->hasMany(Appointment::class, 'PetID', 'PetID');
    }

    public function adopterProfile() {
        return $this->belongsTo(AdopterProfile::class, 'AdopterID', 'UserID');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdoptionApplication extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'adoption_applications';
    protected $primaryKey = 'ApplicationID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $with = ['adopter', 'pet', 'photos'];
    protected $fillable = [
        'ApplicationDate', 'AdoptionStatus', 'AdopterID', 'PetID', 'FullName',
        'Age', 'Gender', 'Address', 'Postcode', 'State', 'ContactNumber', 'Email',
        'Occupation', 'HouseholdDetails', 'OtherPetsInfo', 'ReasonForAdoption',
        'PetCarePlan', 'EmergencyPlan', 'SpayNeuterAgreement', 'ReturnAgreement',
        'ReferenceName', 'ReferenceContact', 'ReferenceRelationship',
        'StaffNotes', 'EstimatedPickupDate', 'ResubmissionCount', 'LastRejectionDate',
        'CancellationReason'
    ];
    protected $casts = [
        'LastRejectionDate' => 'datetime',
    ];

    // Relationships
    public function adopter() {
        return $this->belongsTo(User::class, 'AdopterID', 'UserID');
    }

    public function pet() {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
    }

    public function photos() {
        return $this->hasMany(AdoptionApplicationPhoto::class, 'ApplicationID', 'ApplicationID');
    }
}

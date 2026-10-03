<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $table = 'health_records';
    protected $primaryKey = 'RecordID'; 
    public $timestamps = true; 

    protected $fillable = [
        'PetID',
        'VaccinationStatus',
        'HealthRemarks',
        'Sterilization',
        'LastCheckupDate',
        'Diagnosis', 
        'Medicine'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
    }
    
    public function images()
    {
        return $this->hasMany(PetHealthRecordImage::class, 'RecordID', 'RecordID');
    }
}

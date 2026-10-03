<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetHealthRecordImage extends Model
{
    use HasFactory;
    
    protected $table = 'pet_health_record_images';

    public $incrementing = true;
    
    protected $fillable = ['RecordID', 'ImagePath'];

    public function healthRecord()
    {
        return $this->belongsTo(PetHealthRecord::class, 'RecordID', 'RecordID');
    }
}

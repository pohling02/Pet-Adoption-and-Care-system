<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model {

    use HasFactory;
    
    protected $primaryKey = 'ImageID';

    protected $fillable = [
        'PetID',
        'ImagePath',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
    }
    
//    public function getFirstImageUrlAttribute()
//    {
//        $image = $this->images()->first(); 
//        return $image ? asset('storage/' . $image->ImagePath) : asset('images/default-pet.jpg');
//    }
}

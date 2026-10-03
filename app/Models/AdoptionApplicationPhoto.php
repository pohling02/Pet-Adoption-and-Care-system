<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionApplicationPhoto extends Model {

    use HasFactory;

    protected $table = 'adoption_application_photos'; // Specify the table name
    protected $fillable = ['ApplicationID', 'PhotoPath']; // Mass assignable fields

    public function adoptionApplication() {
        return $this->belongsTo(AdoptionApplication::class, 'ApplicationID', 'ApplicationID');
    }
}

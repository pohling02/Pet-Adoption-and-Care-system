<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}

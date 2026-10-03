<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model {
    use HasFactory;
    
    protected $table = 'notifications';
    protected $primaryKey = 'NotificationID';
    
    protected $fillable = [
        'UserID',
        'type',
        'message',
        'read_at',
        'data',
        'related_id'
    ];
    
    protected $casts = [
        'data' => 'array',
    ];
    
    public function user() {
        return $this->belongsTo(User::class, 'UserID', 'id');
    }
    
    public function notifiable() {
        return $this->morphTo();
    }
    
    public function scopeUnread($query) {
        return $query->whereNull('read_at');
    }
}
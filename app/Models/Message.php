<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {

    use HasFactory;

    protected $primaryKey = 'MessageID';
    protected $fillable = [
        'SenderID',
        'ReceiverID',
        'Content',
        'is_read',
        'file_path',
        'file_type',
        'file_name'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'SenderID');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'ReceiverID');
    }

    public function getFileUrlAttribute() {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}

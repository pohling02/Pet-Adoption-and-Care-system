<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetResource extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'pet_resources';
    protected $fillable = [
        'title',
        'description',
        'type',
        'content',
        'category',
        'image_paths',
        'created_by'
    ];
    protected $casts = [
        'image_paths' => 'array', // <-- This will auto-decode/encode JSON
    ];

    public function author() {
        return $this->belongsTo(User::class, 'created_by', 'UserID');
    }

    public function scopeSearch($query, $search) {
        return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                            ->orWhere('content', 'like', "%{$search}%");
                });
    }

    public function scopeOfCategory($query, $category) {
        return $query->where('category', $category);
    }

    public function scopeOfType($query, $type) {
        return $query->where('type', $type);
    }
}

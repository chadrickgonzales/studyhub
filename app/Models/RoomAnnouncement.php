<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'expires_at'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
} 
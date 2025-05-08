<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_session_id',
        'user_id',
        'joined_at',
        'left_at',
        'duration_minutes'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime'
    ];

    public function studySession()
    {
        return $this->belongsTo(StudySession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsActiveAttribute()
    {
        return !$this->left_at;
    }

    public function getDurationAttribute()
    {
        if (!$this->left_at) {
            return now()->diffInMinutes($this->joined_at);
        }

        return $this->duration_minutes;
    }
} 
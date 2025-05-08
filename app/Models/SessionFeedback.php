<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_session_id',
        'user_id',
        'rating',
        'feedback'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    public function studySession()
    {
        return $this->belongsTo(StudySession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRatingTextAttribute()
    {
        return match($this->rating) {
            1 => 'Poor',
            2 => 'Fair',
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent',
            default => 'Not Rated'
        };
    }
} 
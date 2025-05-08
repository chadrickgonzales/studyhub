<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyRoomNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_shared',
        'color'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_shared' => 'boolean'
    ];

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'study_room_id',
        'is_shared',
        'folder',
        'tags'
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'tags' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }
} 
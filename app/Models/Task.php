<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'assigned_to'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_to' => 'array'
    ];

    // Relationships
    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignments')
                    ->withTimestamps();
    }
} 
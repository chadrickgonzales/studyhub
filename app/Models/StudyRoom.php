<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'privacy',
        'rules',
        'settings',
        'message_count',
        'active_members_count',
        'last_activity_at',
        'is_private',
        'banner_image',
        'created_by'
    ];

    protected $casts = [
        'settings' => 'array',
        'last_activity_at' => 'datetime'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'room_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(StudyRoomMessage::class);
    }

    public function categories()
    {
        return $this->belongsToMany(RoomCategory::class, 'room_category_study_room');
    }

    public function announcements()
    {
        return $this->hasMany(RoomAnnouncement::class);
    }

    public function sessions()
    {
        return $this->hasMany(StudySession::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function todoLists()
    {
        return $this->hasMany(TodoList::class);
    }

    public function goals()
    {
        return $this->hasMany(StudyGoal::class);
    }

    public function calendar()
    {
        return $this->hasOne(SharedCalendar::class);
    }

    public function polls()
    {
        return $this->hasMany(RoomPoll::class);
    }

    public function notes()
    {
        return $this->hasMany(StudyRoomNote::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
} 
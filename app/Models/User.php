<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'bio',
        'education_level',
        'institution',
        'major',
        'reputation_score',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    // Relationships
    public function studyRooms()
    {
        return $this->belongsToMany(StudyRoom::class, 'room_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function createdRooms()
    {
        return $this->hasMany(StudyRoom::class, 'created_by');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function interests()
    {
        return $this->hasMany(UserInterest::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function updateLastActive()
    {
        $this->update(['last_active_at' => now()]);
    }

    public function incrementReputation($points = 1)
    {
        $this->increment('reputation_score', $points);
    }

    public function decrementReputation($points = 1)
    {
        $this->decrement('reputation_score', $points);
    }

    public function addInterest($interest)
    {
        return $this->interests()->create(['interest' => $interest]);
    }

    public function removeInterest($interest)
    {
        return $this->interests()->where('interest', $interest)->delete();
    }

    public function recordActivity($type, $description, $subject = null)
    {
        return $this->activities()->create([
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
        ]);
    }

    public function notify($type, $title, $message, $notifiable = null)
    {
        return $this->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
        ]);
    }
}

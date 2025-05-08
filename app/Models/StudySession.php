<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'title',
        'description',
        'scheduled_at',
        'duration',
        'meeting_link',
        'recording_url',
        'whiteboard_data',
        'is_recurring',
        'recurrence_pattern',
        'status',
        'max_participants'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'array',
        'whiteboard_data' => 'array'
    ];

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'study_session_participants')
                    ->withPivot('joined_at', 'left_at', 'role')
                    ->withTimestamps();
    }

    public function recordings()
    {
        return $this->hasMany(SessionRecording::class);
    }

    public function whiteboard()
    {
        return $this->hasOne(SessionWhiteboard::class);
    }

    public function isUpcoming()
    {
        return $this->scheduled_at > now();
    }

    public function isOngoing()
    {
        return $this->scheduled_at <= now() && 
               $this->scheduled_at->addMinutes($this->duration) > now();
    }

    public function isCompleted()
    {
        return $this->scheduled_at->addMinutes($this->duration) <= now();
    }

    public function attendances()
    {
        return $this->hasMany(SessionAttendance::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(SessionFeedback::class);
    }

    public function getStatusAttribute()
    {
        $now = now();
        
        if ($this->scheduled_at->addMinutes($this->duration) <= $now) {
            return 'completed';
        }
        
        if ($this->scheduled_at <= $now && $now < $this->scheduled_at->addMinutes($this->duration)) {
            return 'in_progress';
        }
        
        return 'upcoming';
    }

    public function getDurationAttribute()
    {
        if (!$this->scheduled_at) {
            return null;
        }

        return $this->scheduled_at->diffInMinutes($this->scheduled_at->addMinutes($this->duration));
    }

    public function getParticipantCountAttribute()
    {
        return $this->participants()->count();
    }

    public function getAverageRatingAttribute()
    {
        return $this->feedbacks()->avg('rating') ?? 0;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopeInProgress($query)
    {
        return $query->where('scheduled_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('scheduled_at->addMinutes(duration)')
                          ->orWhere('scheduled_at->addMinutes(duration)', '>', now());
                    });
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('scheduled_at->addMinutes(duration)')
                    ->where('scheduled_at->addMinutes(duration)', '<=', now());
    }

    public function join(User $user)
    {
        return $this->participants()->attach($user->id, [
            'joined_at' => now()
        ]);
    }

    public function leave(User $user)
    {
        $participation = $this->participants()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->latest()
            ->first();

        if ($participation) {
            $participation->pivot->update([
                'left_at' => now(),
                'duration_minutes' => now()->diffInMinutes($participation->pivot->joined_at)
            ]);
        }

        return $participation;
    }

    public function addFeedback(User $user, int $rating, ?string $feedback = null)
    {
        return $this->feedbacks()->create([
            'user_id' => $user->id,
            'rating' => $rating,
            'feedback' => $feedback
        ]);
    }
} 
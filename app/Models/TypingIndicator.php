<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypingIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'study_room_id',
        'last_typed_at'
    ];

    protected $casts = [
        'last_typed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public static function updateTypingStatus($userId, $studyRoomId)
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'study_room_id' => $studyRoomId],
            ['last_typed_at' => now()]
        );
    }

    public static function getActiveTypers($studyRoomId)
    {
        return static::where('study_room_id', $studyRoomId)
            ->where('last_typed_at', '>', now()->subSeconds(5))
            ->with('user')
            ->get()
            ->pluck('user.name');
    }
} 
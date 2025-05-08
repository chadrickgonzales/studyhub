<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyRoomMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_room_id',
        'user_id',
        'content',
        'parent_id',
        'is_pinned',
        'reactions',
        'attachments'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'reactions' => 'array',
        'attachments' => 'array'
    ];

    public function studyRoom()
    {
        return $this->belongsTo(StudyRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(StudyRoomMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(StudyRoomMessage::class, 'parent_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function addReaction($userId, $reaction)
    {
        $reactions = $this->reactions ?? [];
        if (!isset($reactions[$reaction])) {
            $reactions[$reaction] = [];
        }
        if (!in_array($userId, $reactions[$reaction])) {
            $reactions[$reaction][] = $userId;
        }
        $this->reactions = $reactions;
        $this->save();
    }

    public function removeReaction($userId, $reaction)
    {
        $reactions = $this->reactions ?? [];
        if (isset($reactions[$reaction])) {
            $reactions[$reaction] = array_diff($reactions[$reaction], [$userId]);
            if (empty($reactions[$reaction])) {
                unset($reactions[$reaction]);
            }
        }
        $this->reactions = $reactions;
        $this->save();
    }

    public function markAsRead($userId)
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
    }

    public function scopeUnread($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('read_by')
              ->orWhereJsonDoesntContain('read_by', $userId);
        });
    }
} 
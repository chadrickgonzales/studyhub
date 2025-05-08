<?php

namespace App\Policies;

use App\Models\StudyRoom;
use App\Models\StudyRoomMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyRoomMessagePolicy
{
    use HandlesAuthorization;

    public function create(User $user, StudyRoom $studyRoom)
    {
        return $studyRoom->members()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, StudyRoomMessage $message, StudyRoom $studyRoom)
    {
        return $message->user_id === $user->id || 
               $studyRoom->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function delete(User $user, StudyRoomMessage $message, StudyRoom $studyRoom)
    {
        return $message->user_id === $user->id || 
               $studyRoom->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function react(User $user, StudyRoomMessage $message, StudyRoom $studyRoom)
    {
        return $studyRoom->members()->where('user_id', $user->id)->exists();
    }
} 
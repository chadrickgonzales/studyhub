<?php

namespace App\Policies;

use App\Models\RoomAnnouncement;
use App\Models\StudyRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomAnnouncementPolicy
{
    use HandlesAuthorization;

    public function create(User $user, StudyRoom $studyRoom)
    {
        return $studyRoom->members()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();
    }

    public function update(User $user, RoomAnnouncement $announcement, StudyRoom $studyRoom)
    {
        return $announcement->user_id === $user->id || 
               $studyRoom->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    public function delete(User $user, RoomAnnouncement $announcement, StudyRoom $studyRoom)
    {
        return $announcement->user_id === $user->id || 
               $studyRoom->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }
} 
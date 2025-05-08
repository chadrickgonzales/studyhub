<?php

namespace App\Http\Controllers;

use App\Models\RoomAnnouncement;
use App\Models\StudyRoom;
use Illuminate\Http\Request;

class RoomAnnouncementController extends Controller
{
    public function store(Request $request, StudyRoom $studyRoom)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
            'expires_at' => 'nullable|date|after:now'
        ]);

        $announcement = $studyRoom->announcements()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_pinned' => $request->boolean('is_pinned', false),
            'expires_at' => $validated['expires_at'] ?? null,
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, StudyRoom $studyRoom, RoomAnnouncement $announcement)
    {
        $this->authorize('update', [$announcement, $studyRoom]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
            'expires_at' => 'nullable|date|after:now'
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_pinned' => $request->boolean('is_pinned', false),
            'expires_at' => $validated['expires_at'] ?? null
        ]);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(StudyRoom $studyRoom, RoomAnnouncement $announcement)
    {
        $this->authorize('delete', [$announcement, $studyRoom]);

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully.');
    }
} 
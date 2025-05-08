<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use App\Models\StudySession;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    public function store(Request $request, StudyRoom $studyRoom)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'meeting_link' => 'nullable|url|max:255',
        ]);

        $validated['created_by'] = auth()->id();
        $session = $studyRoom->sessions()->create($validated);

        return back()->with('success', 'Study session scheduled successfully!');
    }

    public function destroy(StudyRoom $studyRoom, StudySession $session)
    {
        if ($session->created_by !== auth()->id()) {
            return back()->with('error', 'You can only cancel sessions you created.');
        }

        $session->delete();
        return back()->with('success', 'Study session cancelled successfully.');
    }
} 
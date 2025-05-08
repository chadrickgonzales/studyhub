<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyRoom::query();

        if ($request->has('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $studyRooms = $query->where('is_private', false)
            ->withCount('members')
            ->latest()
            ->paginate(12);

        return view('study-rooms.index', compact('studyRooms'));
    }

    public function create()
    {
        return view('study-rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_private' => 'boolean',
            'banner_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('room-banners', 'public');
            $validated['banner_image'] = $path;
        }

        $validated['created_by'] = auth()->id();
        $studyRoom = StudyRoom::create($validated);

        // Add creator as admin member
        $studyRoom->members()->attach(auth()->id(), ['role' => 'admin']);

        return redirect()->route('study-rooms.show', $studyRoom)
            ->with('success', 'Study room created successfully!');
    }

    public function show(StudyRoom $studyRoom)
    {
        $studyRoom->load([
            'members',
            'notes' => function ($query) {
                $query->where('is_shared', true)->latest();
            },
            'tasks' => function ($query) {
                $query->orderBy('due_date');
            },
            'messages' => function ($query) {
                $query->with('user')->latest();
            },
            'resources' => function ($query) {
                $query->with('uploader')->latest();
            }
        ]);

        // Get messages from the loaded relationship
        $messages = $studyRoom->messages;
        $resources = $studyRoom->resources;
        $upcomingSessions = $studyRoom->sessions()->where('scheduled_at', '>', now())->orderBy('scheduled_at')->get();

        return view('study-rooms.show', compact('studyRoom', 'messages', 'resources', 'upcomingSessions'));
    }

    public function edit(StudyRoom $studyRoom)
    {
        $this->authorize('update', $studyRoom);

        return view('study-rooms.edit', compact('studyRoom'));
    }

    public function update(Request $request, StudyRoom $studyRoom)
    {
        $this->authorize('update', $studyRoom);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_private' => 'boolean',
            'banner_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('banner_image')) {
            if ($studyRoom->banner_image) {
                Storage::disk('public')->delete($studyRoom->banner_image);
            }
            $path = $request->file('banner_image')->store('room-banners', 'public');
            $validated['banner_image'] = $path;
        }

        $studyRoom->update($validated);

        return redirect()->route('study-rooms.show', $studyRoom)
            ->with('success', 'Study room updated successfully!');
    }

    public function destroy(StudyRoom $studyRoom)
    {
        $this->authorize('delete', $studyRoom);

        if ($studyRoom->banner_image) {
            Storage::disk('public')->delete($studyRoom->banner_image);
        }

        $studyRoom->delete();

        return redirect()->route('study-rooms.index')
            ->with('success', 'Study room deleted successfully!');
    }

    public function join(StudyRoom $studyRoom)
    {
        if ($studyRoom->is_private) {
            return back()->with('error', 'This study room is private.');
        }

        // Check if user is already a member
        if ($studyRoom->members->contains(auth()->id())) {
            return back()->with('error', 'You are already a member of this room.');
        }

        $studyRoom->members()->attach(auth()->id(), ['role' => 'member']);

        return redirect()->route('study-rooms.show', $studyRoom)
            ->with('success', 'Joined study room successfully!');
    }

    public function leave(StudyRoom $studyRoom)
    {
        if ($studyRoom->created_by === auth()->id()) {
            return back()->with('error', 'Room creator cannot leave the room.');
        }

        $studyRoom->members()->detach(auth()->id());

        return redirect()->route('study-rooms.index')
            ->with('success', 'Left study room successfully!');
    }
} 
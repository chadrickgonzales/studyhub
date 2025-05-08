<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\StudyRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function index(StudyRoom $studyRoom)
    {
        $resources = $studyRoom->resources()
            ->with(['user', 'ratings', 'comments'])
            ->latest()
            ->paginate(10);

        return view('resources.index', compact('studyRoom', 'resources'));
    }

    public function create(StudyRoom $studyRoom)
    {
        return view('resources.create', compact('studyRoom'));
    }

    public function store(Request $request, StudyRoom $studyRoom)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:link,file,document',
            'url' => 'required_if:type,link|url|nullable',
            'file' => 'required_if:type,file|file|max:10240|nullable',
        ]);

        $resource = new Resource($validated);
        $resource->user_id = Auth::id();
        $resource->study_room_id = $studyRoom->id;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources');
            $resource->file_path = $path;
        }

        $resource->save();

        return redirect()
            ->route('study-rooms.resources.show', [$studyRoom, $resource])
            ->with('success', 'Resource created successfully.');
    }

    public function show(StudyRoom $studyRoom, Resource $resource)
    {
        $resource->load(['user', 'ratings', 'comments.user']);
        return view('resources.show', compact('studyRoom', 'resource'));
    }

    public function edit(StudyRoom $studyRoom, Resource $resource)
    {
        $this->authorize('update', $resource);
        return view('resources.edit', compact('studyRoom', 'resource'));
    }

    public function update(Request $request, StudyRoom $studyRoom, Resource $resource)
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:link,file,document',
            'url' => 'required_if:type,link|url|nullable',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::delete($resource->file_path);
            }
            $path = $request->file('file')->store('resources');
            $validated['file_path'] = $path;
        }

        $resource->update($validated);

        return redirect()
            ->route('study-rooms.resources.show', [$studyRoom, $resource])
            ->with('success', 'Resource updated successfully.');
    }

    public function destroy(StudyRoom $studyRoom, Resource $resource)
    {
        $this->authorize('delete', $resource);

        if ($resource->file_path) {
            Storage::delete($resource->file_path);
        }

        $resource->delete();

        return redirect()
            ->route('study-rooms.resources.index', $studyRoom)
            ->with('success', 'Resource deleted successfully.');
    }

    public function rate(Request $request, StudyRoom $studyRoom, Resource $resource)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $resource->ratings()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return back()->with('success', 'Rating submitted successfully.');
    }

    public function comment(Request $request, StudyRoom $studyRoom, Resource $resource)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:resource_comments,id',
        ]);

        $comment = $resource->comments()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use App\Models\StudyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyResourceController extends Controller
{
    public function store(Request $request, StudyRoom $studyRoom)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('resources', 'public');

        $resource = $studyRoom->resources()->create([
            'uploaded_by' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Resource uploaded successfully!');
    }

    public function destroy(StudyRoom $studyRoom, StudyResource $resource)
    {
        if ($resource->uploaded_by !== auth()->id()) {
            return back()->with('error', 'You can only delete resources you uploaded.');
        }

        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Resource deleted successfully.');
    }

    public function download(StudyRoom $studyRoom, StudyResource $resource)
    {
        if (!$studyRoom->members()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You must be a member of this study room to download resources.');
        }

        return Storage::disk('public')->download(
            $resource->file_path,
            $resource->name . '.' . pathinfo($resource->file_path, PATHINFO_EXTENSION)
        );
    }
} 
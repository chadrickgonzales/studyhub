<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use App\Models\StudyRoomMessage;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudyRoomMessageController extends Controller
{
    public function store(Request $request, StudyRoom $studyRoom)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:study_room_messages,id',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max
        ]);

        $message = $studyRoom->messages()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'content_html' => Str::markdown($validated['content']),
            'parent_id' => $validated['parent_id'] ?? null,
            'read_by' => [auth()->id()]
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                $message->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        $studyRoom->increment('message_count');
        $studyRoom->touch('last_activity_at');

        return back()->with('success', 'Message sent successfully.');
    }

    public function update(Request $request, StudyRoom $studyRoom, StudyRoomMessage $message)
    {
        $this->authorize('update', [$message, $studyRoom]);

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $message->update([
            'content' => $validated['content'],
            'content_html' => Str::markdown($validated['content']),
            'is_edited' => true,
            'edited_at' => now()
        ]);

        return back()->with('success', 'Message updated successfully.');
    }

    public function destroy(StudyRoom $studyRoom, StudyRoomMessage $message)
    {
        $this->authorize('delete', [$message, $studyRoom]);

        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $message->delete();
        $studyRoom->decrement('message_count');

        return back()->with('success', 'Message deleted successfully.');
    }

    public function react(Request $request, StudyRoom $studyRoom, StudyRoomMessage $message)
    {
        $validated = $request->validate([
            'reaction' => 'required|string|max:50'
        ]);

        if ($request->has('remove')) {
            $message->removeReaction(auth()->id(), $validated['reaction']);
            $action = 'removed from';
        } else {
            $message->addReaction(auth()->id(), $validated['reaction']);
            $action = 'added to';
        }

        return back()->with('success', "Reaction {$action} message.");
    }

    public function markAsRead(StudyRoom $studyRoom, StudyRoomMessage $message)
    {
        $message->markAsRead(auth()->id());
        return response()->json(['success' => true]);
    }

    public function downloadAttachment(StudyRoom $studyRoom, StudyRoomMessage $message, MessageAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name,
            ['Content-Type' => $attachment->mime_type]
        );
    }
} 
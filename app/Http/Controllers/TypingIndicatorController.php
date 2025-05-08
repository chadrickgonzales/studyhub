<?php

namespace App\Http\Controllers;

use App\Models\StudyRoom;
use App\Models\TypingIndicator;
use Illuminate\Http\Request;

class TypingIndicatorController extends Controller
{
    public function update(Request $request, StudyRoom $studyRoom)
    {
        TypingIndicator::updateTypingStatus(auth()->id(), $studyRoom->id);
        return response()->json(['success' => true]);
    }

    public function getTypers(StudyRoom $studyRoom)
    {
        $typers = TypingIndicator::getActiveTypers($studyRoom->id)
            ->reject(fn($name) => $name === auth()->user()->name);

        return response()->json([
            'typers' => $typers,
            'message' => $typers->isEmpty() ? '' : implode(', ', $typers->all()) . ' is typing...'
        ]);
    }
} 
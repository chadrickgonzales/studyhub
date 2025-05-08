<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyRoomController;
use App\Http\Controllers\StudyRoomMessageController;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\StudyResourceController;
use App\Http\Controllers\RoomCategoryController;
use App\Http\Controllers\RoomAnnouncementController;
use App\Http\Controllers\TypingIndicatorController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ResourceController;
use App\Models\StudyRoom;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    $studyRooms = StudyRoom::withCount('members')
        ->whereHas('members', function($query) {
            $query->where('user_id', auth()->id());
        })
        ->latest()
        ->get();

    $upcomingSessions = collect(); // We'll implement this later
    $recentActivity = collect(); // We'll implement this later

    return view('dashboard', compact('studyRooms', 'upcomingSessions', 'recentActivity'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/interests', [ProfileController::class, 'storeInterest'])->name('profile.interests.store');
    Route::delete('/profile/interests/{interest}', [ProfileController::class, 'destroyInterest'])->name('profile.interests.destroy');
    Route::post('/profile/notifications/{notification}/mark-as-read', [ProfileController::class, 'markNotificationAsRead'])->name('profile.notifications.mark-as-read');

    // Study Room Routes
    Route::resource('study-rooms', StudyRoomController::class);
    Route::post('/study-rooms/{studyRoom}/join', [StudyRoomController::class, 'join'])->name('study-rooms.join');
    Route::post('/study-rooms/{studyRoom}/leave', [StudyRoomController::class, 'leave'])->name('study-rooms.leave');

    // Study Room Messages
    Route::post('/study-rooms/{studyRoom}/messages', [StudyRoomMessageController::class, 'store'])->name('study-rooms.messages.store');
    Route::put('/study-rooms/{studyRoom}/messages/{message}', [StudyRoomMessageController::class, 'update'])->name('study-rooms.messages.update');
    Route::delete('/study-rooms/{studyRoom}/messages/{message}', [StudyRoomMessageController::class, 'destroy'])->name('study-rooms.messages.destroy');

    // Study Sessions
    Route::post('/study-rooms/{studyRoom}/sessions', [StudySessionController::class, 'store'])->name('study-rooms.sessions.store');
    Route::delete('/study-rooms/{studyRoom}/sessions/{session}', [StudySessionController::class, 'destroy'])->name('study-rooms.sessions.destroy');

    // Study Resources
    Route::post('/study-rooms/{studyRoom}/resources', [StudyResourceController::class, 'store'])->name('study-rooms.resources.store');
    Route::get('/study-rooms/{studyRoom}/resources/{resource}/download', [StudyResourceController::class, 'download'])->name('study-rooms.resources.download');
    Route::delete('/study-rooms/{studyRoom}/resources/{resource}', [StudyResourceController::class, 'destroy'])->name('study-rooms.resources.destroy');

    // Room Categories
    Route::resource('room-categories', RoomCategoryController::class);
    
    // Room Announcements
    Route::post('/study-rooms/{studyRoom}/announcements', [RoomAnnouncementController::class, 'store'])->name('study-rooms.announcements.store');
    Route::put('/study-rooms/{studyRoom}/announcements/{announcement}', [RoomAnnouncementController::class, 'update'])->name('study-rooms.announcements.update');
    Route::delete('/study-rooms/{studyRoom}/announcements/{announcement}', [RoomAnnouncementController::class, 'destroy'])->name('study-rooms.announcements.destroy');
    
    // Enhanced Chat Features
    Route::post('/study-rooms/{studyRoom}/messages/{message}/react', [StudyRoomMessageController::class, 'react'])->name('study-rooms.messages.react');
    Route::post('/study-rooms/{studyRoom}/messages/{message}/read', [StudyRoomMessageController::class, 'markAsRead'])->name('study-rooms.messages.read');
    Route::get('/study-rooms/{studyRoom}/messages/{message}/attachments/{attachment}/download', [StudyRoomMessageController::class, 'downloadAttachment'])->name('study-rooms.messages.attachment.download');
    
    // Typing Indicators
    Route::post('/study-rooms/{studyRoom}/typing', [TypingIndicatorController::class, 'update'])->name('study-rooms.typing.update');
    Route::get('/study-rooms/{studyRoom}/typing', [TypingIndicatorController::class, 'getTypers'])->name('study-rooms.typing.get');

    // Resource Management
    Route::resource('announcements', AnnouncementController::class);
    
    Route::resource('resources', ResourceController::class);
    Route::post('resources/{resource}/rate', [ResourceController::class, 'rate'])->name('resources.rate');
    Route::post('resources/{resource}/comment', [ResourceController::class, 'comment'])->name('resources.comment');
});

require __DIR__.'/auth.php';

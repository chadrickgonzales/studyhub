<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserInterest;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'education_level' => ['nullable', 'string', 'in:high_school,undergraduate,graduate,phd'],
            'institution' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($request->user()->profile_picture) {
                Storage::disk('public')->delete($request->user()->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $request->user()->update($validated);

        // Record activity
        $request->user()->recordActivity(
            'profile_update',
            'Updated profile information'
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function storeInterest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'interest' => ['required', 'string', 'max:50'],
        ]);

        $request->user()->addInterest($validated['interest']);

        // Record activity
        $request->user()->recordActivity(
            'interest_added',
            'Added new interest: ' . $validated['interest']
        );

        return back()->with('status', 'interest-added');
    }

    public function destroyInterest(UserInterest $interest): RedirectResponse
    {
        if ($interest->user_id !== auth()->id()) {
            abort(403);
        }

        $interest->delete();

        // Record activity
        auth()->user()->recordActivity(
            'interest_removed',
            'Removed interest: ' . $interest->interest
        );

        return back()->with('status', 'interest-removed');
    }

    public function markNotificationAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('status', 'notification-marked-as-read');
    }
}

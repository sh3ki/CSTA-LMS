<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.settings');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name'       => 'required|string|max:255',
            'contact_number'  => 'nullable|string|max:30',
        ]);

        $user->update([
            'full_name'      => $request->full_name,
            'contact_number' => $request->contact_number,
        ]);

        AuditLog::record('profile_update', 'Updated profile information.');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = auth()->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        AuditLog::record('profile_photo_update', 'Updated profile picture.');

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function removePhoto()
    {
        $user = auth()->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return back()->with('success', 'Profile picture removed.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => $request->password]);

        AuditLog::record('password_change', 'Changed account password.');

        return back()->with('success', 'Password changed successfully.');
    }
}

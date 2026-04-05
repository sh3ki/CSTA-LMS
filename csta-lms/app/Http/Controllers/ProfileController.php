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
        $user = $request->user();

        $request->validate([
            'full_name'       => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact_number'  => 'nullable|string|max:30',
            'course'          => 'nullable|string|max:255',
            'year_level'      => 'nullable|string|max:50',
        ]);

        $user->update([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'course'         => $user->role === 'student' ? $request->course : null,
            'year_level'     => $user->role === 'student' ? $request->year_level : null,
        ]);

        AuditLog::record('profile_update', 'Updated profile information.');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        AuditLog::record('profile_photo_update', 'Updated profile picture.');

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function removePhoto(Request $request)
    {
        $user = $request->user();

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

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => $request->password]);

        AuditLog::record('password_change', 'Changed account password.');

        return back()->with('success', 'Password changed successfully.');
    }
}

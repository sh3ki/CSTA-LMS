<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role . '.dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role . '.dashboard');
        }

        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_number' => 'required',
            'password'  => 'required',
        ], [
            'id_number.required' => 'ID Number is required.',
            'password.required'  => 'Password is required.',
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        if (!$user) {
            return back()->withErrors(['id_number' => 'Invalid ID Number or Password.'])->withInput();
        }

        if (!$user->status) {
            return back()->withErrors(['id_number' => 'Your account has been disabled. Please contact the administrator.'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['id_number' => 'Invalid ID Number or Password.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        AuditLog::record('Login', 'User logged in', $user);

        return redirect()->route($user->role . '.dashboard');
    }

    public function logout(Request $request)
    {
        AuditLog::record('Logout', 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:teacher,student',
            'id_number' => 'required|string|max:50|unique:users,id_number',
            'email' => 'required|email|max:255|unique:users,email',
            'contact_number' => 'nullable|string|max:30',
            'course' => 'required_if:role,student|nullable|string|max:255',
            'year_level' => 'required_if:role,student|nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'agree_terms' => 'accepted',
        ], [
            'agree_terms.accepted' => 'You must agree to the terms and conditions to continue.',
            'course.required_if' => 'Course is required for student registration.',
            'year_level.required_if' => 'Year level is required for student registration.',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'role' => $request->role,
            'id_number' => $request->id_number,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'course' => $request->role === 'student' ? $request->course : null,
            'year_level' => $request->role === 'student' ? $request->year_level : null,
            'password' => Hash::make($request->password),
            'status' => true,
        ]);

        AuditLog::record('Register', "Registered new {$user->role} account: {$user->full_name} ({$user->id_number})", $user);

        Auth::login($user);

        return redirect()->route($user->role . '.dashboard')->with('success', 'Registration completed successfully.');
    }
}

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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'teacher');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('full_name', 'like', "%$q%")
                   ->orWhere('id_number', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhere('contact_number', 'like', "%$q%");
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $teachers = $query->orderBy('full_name')->paginate(10)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email',
            'contact_number' => 'nullable|string|max:20',
            'id_number'      => 'required|string|max:50|unique:users,id_number',
            'password'       => 'required|string|min:6|confirmed',
            'profile_picture'=> 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_picture')) {
            $photoPath = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $teacher = User::create([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'id_number'      => $request->id_number,
            'profile_picture'=> $photoPath,
            'password'       => Hash::make($request->password),
            'role'           => 'teacher',
            'status'         => true,
        ]);

        AuditLog::record('Create Teacher', "Created teacher account: {$teacher->full_name} ({$teacher->id_number})");

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher account created successfully.');
    }

    public function update(Request $request, User $teacher)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $teacher->id,
            'contact_number' => 'nullable|string|max:20',
            'id_number'      => 'required|string|max:50|unique:users,id_number,' . $teacher->id,
            'profile_picture'=> 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'id_number'      => $request->id_number,
        ];

        if ($request->hasFile('profile_picture')) {
            if ($teacher->profile_picture) {
                Storage::disk('public')->delete($teacher->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $teacher->update($data);

        AuditLog::record('Edit Teacher', "Updated teacher account: {$teacher->full_name} ({$teacher->id_number})");

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function toggleStatus(User $teacher)
    {
        $teacher->update(['status' => !$teacher->status]);
        $action = $teacher->status ? 'Activated' : 'Deactivated';
        AuditLog::record("$action Teacher", "$action teacher: {$teacher->full_name} ({$teacher->id_number})");
        return redirect()->route('admin.teachers.index')->with('success', "Teacher {$action} successfully.");
    }

    public function changePassword(Request $request, User $teacher)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $teacher->update(['password' => Hash::make($request->password)]);
        AuditLog::record('Change Teacher Password', "Changed password for teacher: {$teacher->full_name} ({$teacher->id_number})");

        return redirect()->route('admin.teachers.index')->with('success', 'Password changed successfully.');
    }

    public function destroy(User $teacher)
    {
        $name = $teacher->full_name;
        $id   = $teacher->id_number;
        $teacher->delete();
        AuditLog::record('Delete Teacher', "Deleted teacher account: {$name} ({$id})");
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file     = $request->file('csv_file');
        $handle   = fopen($file->getRealPath(), 'r');
        $headers  = fgetcsv($handle); // skip header row
        $imported = 0;
        $errors   = [];
        $row      = 1;

        if ($headers) {
            $headers = array_map('trim', $headers);
        }

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (!$headers || count($data) < count($headers)) {
                continue;
            }
            $record = array_combine($headers, $data);

            if (empty($record['id_number']) || empty($record['full_name']) || empty($record['password'])) {
                $errors[] = "Row $row: Missing required fields (id_number, full_name, password).";
                continue;
            }

            if (User::where('id_number', trim($record['id_number']))->exists()) {
                $errors[] = "Row $row: ID Number '{$record['id_number']}' already exists.";
                continue;
            }

            User::create([
                'id_number'      => trim($record['id_number']),
                'email'          => trim($record['email'] ?? '') ?: null,
                'full_name'      => trim($record['full_name']),
                'contact_number' => trim($record['contact_number'] ?? ''),
                'password'       => Hash::make(trim($record['password'])),
                'role'           => 'teacher',
                'status'         => true,
            ]);
            $imported++;
        }
        fclose($handle);

        AuditLog::record('Import Teachers', "Imported {$imported} teacher(s) via CSV.");

        $msg = "Imported {$imported} teacher(s) successfully.";
        if (!empty($errors)) {
            $msg .= ' Errors: ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.teachers.index')->with('success', $msg);
    }
}

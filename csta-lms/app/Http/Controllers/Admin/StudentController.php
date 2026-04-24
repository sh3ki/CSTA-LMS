<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    private const COURSE_OPTIONS = ['BSIT', 'BSED', 'BSHM', 'BSTM'];

    public function index(Request $request)
    {
        $query = User::where('role', 'student');

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

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        $courses = User::where('role', 'student')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->select('course')
            ->distinct()
            ->orderBy('course')
            ->pluck('course')
            ->values();

        $students = $query->orderBy('full_name')->paginate(10)->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'courses' => $courses,
            'courseOptions' => self::COURSE_OPTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email',
            'contact_number' => 'nullable|string|max:20',
            'id_number'      => 'required|string|max:50|unique:users,id_number',
            'course'         => 'required|string|in:' . implode(',', self::COURSE_OPTIONS),
            'year_level'     => 'nullable|string|max:50',
            'password'       => 'required|string|min:6|confirmed',
            'profile_picture'=> 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_picture')) {
            $photoPath = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $student = User::create([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'id_number'      => $request->id_number,
            'course'         => $request->course,
            'year_level'     => $request->year_level,
            'profile_picture'=> $photoPath,
            'password'       => Hash::make($request->password),
            'role'           => 'student',
            'status'         => true,
        ]);

        AuditLog::record('Create Student', "Created student account: {$student->full_name} ({$student->id_number})");

        return redirect()->route('admin.students.index')->with('success', 'Student account created successfully.');
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $student->id,
            'contact_number' => 'nullable|string|max:20',
            'id_number'      => 'required|string|max:50|unique:users,id_number,' . $student->id,
            'course'         => 'required|string|in:' . implode(',', self::COURSE_OPTIONS),
            'year_level'     => 'nullable|string|max:50',
            'profile_picture'=> 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
            'id_number'      => $request->id_number,
            'course'         => $request->course,
            'year_level'     => $request->year_level,
        ];

        if ($request->hasFile('profile_picture')) {
            if ($student->profile_picture) {
                Storage::disk('public')->delete($student->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $student->update($data);

        AuditLog::record('Edit Student', "Updated student account: {$student->full_name} ({$student->id_number})");

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    public function toggleStatus(User $student)
    {
        $student->update(['status' => !$student->status]);
        $action = $student->status ? 'Activated' : 'Deactivated';
        AuditLog::record("$action Student", "$action student: {$student->full_name} ({$student->id_number})");
        return redirect()->route('admin.students.index')->with('success', "Student {$action} successfully.");
    }

    public function changePassword(Request $request, User $student)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $student->update(['password' => Hash::make($request->password)]);
        AuditLog::record('Change Student Password', "Changed password for student: {$student->full_name} ({$student->id_number})");

        return redirect()->route('admin.students.index')->with('success', 'Password changed successfully.');
    }

    public function destroy(User $student)
    {
        $name = $student->full_name;
        $id   = $student->id_number;
        $student->delete();
        AuditLog::record('Delete Student', "Deleted student account: {$name} ({$id})");
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file     = $request->file('csv_file');
        $handle   = fopen($file->getRealPath(), 'r');
        $headers  = fgetcsv($handle);
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
                'course'         => trim($record['course'] ?? '') ?: null,
                'year_level'     => trim($record['year_level'] ?? '') ?: null,
                'password'       => Hash::make(trim($record['password'])),
                'role'           => 'student',
                'status'         => true,
            ]);
            $imported++;
        }
        fclose($handle);

        AuditLog::record('Import Students', "Imported {$imported} student(s) via CSV.");

        $msg = "Imported {$imported} student(s) successfully.";
        if (!empty($errors)) {
            $msg .= ' Errors: ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.students.index')->with('success', $msg);
    }
}

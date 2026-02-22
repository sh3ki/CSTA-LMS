<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::with(['teacher', 'students']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('name', 'like', "%$q%");
        }

        $classes  = $query->orderBy('name')->paginate(10)->withQueryString();
        $teachers = User::where('role', 'teacher')->where('status', true)->orderBy('full_name')->get();
        $students = User::where('role', 'student')->where('status', true)->orderBy('full_name')->get();

        return view('admin.classes.index', compact('classes', 'teachers', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'students'   => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $class = SchoolClass::create([
            'name'       => $request->name,
            'teacher_id' => $request->teacher_id,
        ]);

        if ($request->filled('students')) {
            $class->students()->sync($request->students);
        }

        AuditLog::record('Create Class', "Created class: {$class->name}");

        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['teacher', 'students', 'subjects']);
        return view('admin.classes.show', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'students'   => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $class->update([
            'name'       => $request->name,
            'teacher_id' => $request->teacher_id,
        ]);

        $class->students()->sync($request->students ?? []);

        AuditLog::record('Edit Class', "Updated class: {$class->name}");

        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $name = $class->name;
        $class->students()->detach();
        $class->delete();
        AuditLog::record('Delete Class', "Deleted class: {$name}");
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully.');
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

            if (empty($record['name'])) {
                $errors[] = "Row $row: Missing class name.";
                continue;
            }

            $teacher = null;
            if (!empty($record['teacher_id_number'])) {
                $teacher = User::where('id_number', trim($record['teacher_id_number']))
                               ->where('role', 'teacher')->first();
            }

            SchoolClass::create([
                'name'       => trim($record['name']),
                'teacher_id' => $teacher?->id,
            ]);
            $imported++;
        }
        fclose($handle);

        AuditLog::record('Import Classes', "Imported {$imported} class(es) via CSV.");

        $msg = "Imported {$imported} class(es) successfully.";
        if (!empty($errors)) {
            $msg .= ' Errors: ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.classes.index')->with('success', $msg);
    }
}

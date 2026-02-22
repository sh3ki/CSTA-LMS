<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('schoolClass');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $subjects = $query->orderBy('name')->paginate(10)->withQueryString();
        $classes  = SchoolClass::orderBy('name')->get();

        return view('admin.subjects.index', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'class_id'    => 'nullable|exists:classes,id',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create([
            'name'        => $request->name,
            'class_id'    => $request->class_id,
            'description' => $request->description,
        ]);

        AuditLog::record('Create Subject', "Created subject: {$subject->name}");

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        $subject->load('schoolClass');
        return view('admin.subjects.show', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'class_id'    => 'nullable|exists:classes,id',
            'description' => 'nullable|string',
        ]);

        $subject->update([
            'name'        => $request->name,
            'class_id'    => $request->class_id,
            'description' => $request->description,
        ]);

        AuditLog::record('Edit Subject', "Updated subject: {$subject->name}");

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $name = $subject->name;
        $subject->delete();
        AuditLog::record('Delete Subject', "Deleted subject: {$name}");
        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
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
                $errors[] = "Row $row: Missing subject name.";
                continue;
            }

            $class = null;
            if (!empty($record['class_name'])) {
                $class = SchoolClass::where('name', trim($record['class_name']))->first();
            }

            Subject::create([
                'name'        => trim($record['name']),
                'class_id'    => $class?->id,
                'description' => trim($record['description'] ?? ''),
            ]);
            $imported++;
        }
        fclose($handle);

        AuditLog::record('Import Subjects', "Imported {$imported} subject(s) via CSV.");

        $msg = "Imported {$imported} subject(s) successfully.";
        if (!empty($errors)) {
            $msg .= ' Errors: ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.subjects.index')->with('success', $msg);
    }
}

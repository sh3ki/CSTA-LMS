<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();

        $query = SchoolClass::with(['students', 'subjects'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($studentQuery) use ($search) {
                        $studentQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('id_number', 'like', "%{$search}%");
                    });
            });
        }

        $classList = $query->orderBy('name')->paginate(10)->withQueryString();
        $students = User::where('role', 'student')
            ->where('status', true)
            ->whereHas('enrolledClasses', function ($classQuery) use ($teacher) {
                $classQuery->where('teacher_id', $teacher->id);
            })
            ->orderBy('full_name')
            ->distinct()
            ->get();

        return view('teacher.classes.index', compact('classList', 'students'));
    }

    public function show(SchoolClass $class)
    {
        $teacher = Auth::user();
        if (!$teacher || (int) $class->teacher_id !== (int) $teacher->id) {
            abort(403, 'Unauthorized access.');
        }

        $class->load(['students', 'subjects']);

        return view('teacher.classes.show', compact('class'));
    }

    public function store(Request $request)
    {
        $teacher = $request->user();

        $allowedStudentIds = User::where('role', 'student')
            ->where('status', true)
            ->whereHas('enrolledClasses', function ($classQuery) use ($teacher) {
                $classQuery->where('teacher_id', $teacher->id);
            })
            ->pluck('id')
            ->all();

        $request->validate([
            'name' => 'required|string|max:255',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $selectedStudents = collect($request->input('students', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $invalidStudents = $selectedStudents->diff($allowedStudentIds);
        if ($invalidStudents->isNotEmpty()) {
            return redirect()
                ->route('teacher.classes.index')
                ->withErrors(['students' => 'Only students currently enrolled in your own classes can be assigned.'])
                ->withInput();
        }

        $class = SchoolClass::create([
            'name' => $request->name,
            'teacher_id' => $teacher->id,
            'status' => true,
        ]);

        if ($selectedStudents->isNotEmpty()) {
            $class->students()->sync($selectedStudents->all());
        }

        AuditLog::record('Create Class', "Teacher created class: {$class->name}");

        return redirect()->route('teacher.classes.index')->with('success', 'Class created successfully.');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $teacher = Auth::user();
        if (!$teacher || (int) $class->teacher_id !== (int) $teacher->id) {
            abort(403, 'Unauthorized access.');
        }

        $allowedStudentIds = User::where('role', 'student')
            ->where('status', true)
            ->whereHas('enrolledClasses', function ($classQuery) use ($teacher) {
                $classQuery->where('teacher_id', $teacher->id);
            })
            ->pluck('id')
            ->all();

        $request->validate([
            'name' => 'required|string|max:255',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $selectedStudents = collect($request->input('students', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $invalidStudents = $selectedStudents->diff($allowedStudentIds);
        if ($invalidStudents->isNotEmpty()) {
            return redirect()
                ->route('teacher.classes.index')
                ->withErrors(['students' => 'Only students currently enrolled in your own classes can be assigned.'])
                ->withInput();
        }

        $class->update([
            'name' => $request->name,
        ]);

        $class->students()->sync($selectedStudents->all());

        AuditLog::record('Edit Class', "Teacher updated class: {$class->name}");

        return redirect()->route('teacher.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $teacher = Auth::user();
        if (!$teacher || (int) $class->teacher_id !== (int) $teacher->id) {
            abort(403, 'Unauthorized access.');
        }

        $name = $class->name;
        $class->students()->detach();
        $class->delete();

        AuditLog::record('Delete Class', "Teacher deleted class: {$name}");

        return redirect()->route('teacher.classes.index')->with('success', 'Class deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Get classes assigned to this teacher
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->where('status', true)->pluck('id');

        $query = Subject::with(['schoolClass.students', 'resources', 'tasks'])
            ->whereIn('class_id', $classIds);

        $query->where('status', true);

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
        $teacherClasses = SchoolClass::where('teacher_id', $teacher->id)->where('status', true)->orderBy('name')->get();

        return view('teacher.subjects.index', compact('subjects', 'teacherClasses'));
    }

    public function show(Request $request, Subject $subject)
    {
        $teacher = $request->user();

        // Ensure this subject belongs to a class the teacher owns
        $ownsSubject = SchoolClass::where('teacher_id', $teacher->id)
            ->where('id', $subject->class_id)
            ->exists();

        if (!$ownsSubject) {
            abort(403, 'Unauthorized access.');
        }

        $subject->load(['schoolClass.students', 'resources.uploader', 'tasks.creator']);

        return view('teacher.subjects.show', compact('subject'));
    }

    public function store(Request $request)
    {
        $teacher = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'course_code' => 'nullable|string|max:100',
            'semester' => 'required|in:1st,2nd,3rd',
            'description' => 'nullable|string',
        ]);

        $class = SchoolClass::where('id', $request->class_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $subject = Subject::create([
            'name' => $request->name,
            'subject_code' => Subject::generateUniqueCode(),
            'course_code' => $request->course_code,
            'semester' => $request->semester,
            'class_id' => $class->id,
            'description' => $request->description,
            'status' => true,
            'created_by' => $teacher->id,
        ]);

        AuditLog::record('Create Subject', "Teacher created subject: {$subject->name} ({$subject->subject_code})");

        return redirect()
            ->route('teacher.subjects.index')
            ->with('success', 'Subject created successfully.')
            ->with('created_subject_code', $subject->subject_code)
            ->with('created_subject_name', $subject->name);
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        $classIds = SchoolClass::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })->where('status', true)->pluck('id');

        $query = Subject::with(['schoolClass.teacher', 'resources', 'tasks'])
            ->withCount(['tasks as pending_tasks_count' => function ($taskQuery) use ($student) {
                $taskQuery->whereDoesntHave('submissions', function ($submissionQuery) use ($student) {
                    $submissionQuery->where('student_id', $student->id);
                });
            }])
            ->whereIn('class_id', $classIds)
            ->where('status', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $subjects = $query->orderBy('name')->paginate(12)->withQueryString();
        $classes = SchoolClass::whereIn('id', $classIds)->orderBy('name')->get();

        return view('student.subjects.index', compact('subjects', 'classes'));
    }

    public function show(Request $request, Subject $subject)
    {
        $student = $request->user();

        $enrolled = SchoolClass::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })->where('id', $subject->class_id)->exists();

        if (!$enrolled) {
            abort(403, 'Unauthorized access.');
        }

        $subject->load(['schoolClass.teacher', 'resources.uploader', 'tasks' => function ($query) {
            $query->with('creator')->orderByDesc('created_at');
        }]);

        $submissions = Submission::where('student_id', $student->id)
            ->whereIn('task_id', $subject->tasks->pluck('id'))
            ->get()
            ->keyBy('task_id');

        $students = $subject->schoolClass ? $subject->schoolClass->students : collect();

        $streamType = $request->input('stream_type', 'all');
        if (!in_array($streamType, ['all', 'resources', 'tasks'], true)) {
            $streamType = 'all';
        }

        $pendingTaskCount = $subject->tasks->filter(function ($task) use ($submissions) {
            return !isset($submissions[$task->id]);
        })->count();

        return view('student.subjects.show', compact('subject', 'submissions', 'students', 'streamType', 'pendingTaskCount'));
    }

    public function joinByCode(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|max:50',
        ]);

        $student = $request->user();

        $subject = Subject::where('subject_code', strtoupper(trim($request->subject_code)))
            ->where('status', true)
            ->first();

        if (!$subject || !$subject->class_id) {
            return redirect()->route('student.subjects.index')->with('error', 'Invalid or inactive subject code.');
        }

        $class = SchoolClass::where('id', $subject->class_id)
            ->where('status', true)
            ->first();

        if (!$class) {
            return redirect()->route('student.subjects.index')->with('error', 'This subject is not available for joining.');
        }

        if (!$class->students()->where('users.id', $student->id)->exists()) {
            $class->students()->attach($student->id);
        }

        return redirect()->route('student.subjects.show', $subject)->with('success', 'Successfully joined subject using class code.');
    }

    public function downloadResource(Request $request, Resource $resource)
    {
        $student = $request->user();
        $resource->loadMissing('subject');

        if (!$resource->subject || !$resource->subject->class_id) {
            abort(404);
        }

        $enrolled = SchoolClass::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })->where('id', $resource->subject->class_id)->exists();

        if (!$enrolled) {
            abort(403, 'Unauthorized access.');
        }

        if (!$resource->file_path) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($resource->file_path), $resource->file_name);
    }
}
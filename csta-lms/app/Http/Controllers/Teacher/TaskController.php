<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Check if the given subject belongs to the authenticated teacher via DB query.
     */
    private function ownsSubject($subjectId): bool
    {
        $userId   = auth()->user()->id;
        $classIds = SchoolClass::where('teacher_id', $userId)->pluck('id');
        return Subject::where('id', $subjectId)->whereIn('class_id', $classIds)->exists();
    }

    public function index(Request $request)
    {
        $teacher    = auth()->user();
        $classIds   = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');
        $subjectIds = Subject::whereIn('class_id', $classIds)->pluck('id');

        $query = Task::with(['subject.schoolClass', 'submissions'])
            ->whereIn('subject_id', $subjectIds);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('due_date', '>', now());
            } elseif ($request->status === 'past_due') {
                $query->where('due_date', '<=', now());
            }
        }

        $tasks    = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $subjects = Subject::whereIn('class_id', $classIds)->orderBy('name')->get();

        return view('teacher.tasks.index', compact('tasks', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'required|date|after:now',
            'total_points' => 'required|integer|min:1|max:1000',
            'file'         => 'nullable|file|max:20480',
        ]);

        if (!$this->ownsSubject($request->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $data = [
            'subject_id'   => $request->subject_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'due_date'     => $request->due_date,
            'total_points' => $request->total_points,
            'created_by'   => auth()->user()->id,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('tasks', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $task = Task::create($data);

        AuditLog::record('Create Task', "Created task: {$task->title}");

        if ($request->redirect_to === 'subject_show') {
            return redirect()->route('teacher.subjects.show', $request->subject_id)->with('success', 'Task created successfully.');
        }

        return redirect()->route('teacher.tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        if (!$this->ownsSubject($task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $task->load(['subject.schoolClass.students', 'submissions.student']);

        // Get all students in the class and their submission status
        $students    = $task->subject->schoolClass->students ?? collect();
        $submissions = $task->submissions->keyBy('student_id');

        return view('teacher.tasks.show', compact('task', 'students', 'submissions'));
    }

    public function update(Request $request, Task $task)
    {
        if (!$this->ownsSubject($task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'subject_id'   => 'required|exists:subjects,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'required|date',
            'total_points' => 'required|integer|min:1|max:1000',
            'file'         => 'nullable|file|max:20480',
        ]);

        if (!$this->ownsSubject($request->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $data = [
            'subject_id'   => $request->subject_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'due_date'     => $request->due_date,
            'total_points' => $request->total_points,
        ];

        if ($request->hasFile('file')) {
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('tasks', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $task->update($data);

        AuditLog::record('Edit Task', "Updated task: {$task->title}");

        return redirect()->route('teacher.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task)
    {
        if (!$this->ownsSubject($task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $name = $task->title;

        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();

        AuditLog::record('Delete Task', "Deleted task: {$name}");

        if ($request->redirect_to === 'subject_show' && $request->subject_id) {
            return redirect()->route('teacher.subjects.show', $request->subject_id)->with('success', 'Task deleted successfully.');
        }

        return redirect()->route('teacher.tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function grade(Request $request, Submission $submission)
    {
        // Verify teacher owns this task's subject
        if (!$this->ownsSubject($submission->task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'grade'    => 'required|numeric|min:0|max:' . $submission->task->total_points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
        ]);

        AuditLog::record('Grade Submission', "Graded {$submission->student->full_name} on task: {$submission->task->title} — {$request->grade}/{$submission->task->total_points}");

        return redirect()->route('teacher.tasks.show', $submission->task_id)->with('success', 'Grade saved successfully.');
    }

    public function downloadAttachment(Task $task)
    {
        if (!$this->ownsSubject($task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$task->file_path) {
            abort(404);
        }

        return Storage::disk('public')->download($task->file_path, $task->file_name);
    }

    public function downloadSubmission(Submission $submission)
    {
        if (!$this->ownsSubject($submission->task->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$submission->file_path) {
            abort(404);
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\SubmissionHistory;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    private function subjectIdsForStudent(int $studentId)
    {
        $classIds = SchoolClass::whereHas('students', function ($query) use ($studentId) {
            $query->where('users.id', $studentId);
        })->pluck('id');

        return Subject::whereIn('class_id', $classIds)->pluck('id');
    }

    private function canAccessTask(Task $task, int $studentId): bool
    {
        return Task::whereKey($task->id)
            ->whereHas('subject', function ($query) use ($studentId) {
                $query->whereIn('class_id', SchoolClass::whereHas('students', function ($studentQuery) use ($studentId) {
                    $studentQuery->where('users.id', $studentId);
                })->pluck('id'));
            })
            ->exists();
    }

    public function index(Request $request)
    {
        $student = $request->user();
        $subjectIds = $this->subjectIdsForStudent($student->id);

        $query = Task::with(['subject.schoolClass'])
            ->whereIn('subject_id', $subjectIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            if ($request->status === Submission::STATUS_ON_TIME) {
                $query->whereHas('submissions', function ($submissionQuery) use ($student) {
                    $submissionQuery->where('student_id', $student->id)
                        ->whereNotNull('submitted_at')
                        ->whereColumn('submitted_at', '<=', 'tasks.due_date');
                });
            } elseif ($request->status === Submission::STATUS_LATE) {
                $query->whereHas('submissions', function ($submissionQuery) use ($student) {
                    $submissionQuery->where('student_id', $student->id)
                        ->whereNotNull('submitted_at')
                        ->whereColumn('submitted_at', '>', 'tasks.due_date');
                });
            } elseif ($request->status === Submission::STATUS_MISSING) {
                $query->whereDoesntHave('submissions', function ($submissionQuery) use ($student) {
                    $submissionQuery->where('student_id', $student->id);
                });
            }
        }

        $tasks = $query->orderByDesc('due_date')->paginate(10)->withQueryString();

        $submissions = Submission::where('student_id', $student->id)
            ->whereIn('task_id', $tasks->pluck('id'))
            ->get()
            ->keyBy('task_id');

        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        return view('student.tasks.index', compact('tasks', 'subjects', 'submissions'));
    }

    public function show(Task $task)
    {
        $student = request()->user();

        if (!$this->canAccessTask($task, $student->id)) {
            abort(403, 'Unauthorized access.');
        }

        $task->load(['subject.schoolClass.teacher']);

        $submission = Submission::where('student_id', $student->id)
            ->where('task_id', $task->id)
            ->first();

        $submissionHistory = $submission
            ? SubmissionHistory::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->orderByDesc('attempt_number')
                ->get()
            : collect();

        return view('student.tasks.show', compact('task', 'submission', 'submissionHistory'));
    }

    public function submit(Request $request, Task $task)
    {
        $student = $request->user();

        if (!$this->canAccessTask($task, $student->id)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'file' => 'required|file|max:512000',
            'submission_note' => 'nullable|string|max:2000',
        ]);

        $submission = Submission::where('student_id', $student->id)
            ->where('task_id', $task->id)
            ->first();

        if ($submission && !$submission->allow_resubmit) {
            return redirect()
                ->route('student.tasks.show', $task)
                ->with('error', 'Resubmission is currently disabled by your teacher for this task.');
        }

        $file = $request->file('file');
        $storedPath = $file->store('submissions', 'public');

        if (!$submission) {
            $submission = Submission::create([
                'task_id' => $task->id,
                'student_id' => $student->id,
                'file_path' => $storedPath,
                'file_name' => $file->getClientOriginalName(),
                'submission_note' => $request->submission_note,
                'allow_resubmit' => false,
                'submitted_at' => now(),
            ]);
        } else {
            $hasHistory = SubmissionHistory::where('task_id', $task->id)
                ->where('student_id', $student->id)
                ->exists();

            // Backfill first-attempt history for legacy rows that existed before history tracking.
            if (!$hasHistory && $submission->file_path) {
                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'task_id' => $task->id,
                    'student_id' => $student->id,
                    'attempt_number' => 1,
                    'file_path' => $submission->file_path,
                    'file_name' => $submission->file_name,
                    'submission_note' => $submission->submission_note,
                    'submitted_at' => $submission->submitted_at ?? now(),
                ]);
            }

            $submission->update([
                'file_path' => $storedPath,
                'file_name' => $file->getClientOriginalName(),
                'submission_note' => $request->submission_note,
                'allow_resubmit' => false,
                'submitted_at' => now(),
            ]);
        }

        $attempt = (int) SubmissionHistory::where('task_id', $task->id)
            ->where('student_id', $student->id)
            ->max('attempt_number');

        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'task_id' => $task->id,
            'student_id' => $student->id,
            'attempt_number' => $attempt + 1,
            'file_path' => $submission->file_path,
            'file_name' => $submission->file_name,
            'submission_note' => $submission->submission_note,
            'submitted_at' => $submission->submitted_at,
        ]);

        AuditLog::record($submission ? 'Resubmit Task' : 'Submit Task', "Submitted task: {$task->title}");

        return redirect()->route('student.tasks.show', $task)->with('success', $submission ? 'Task resubmitted successfully.' : 'Task submitted successfully.');
    }

    public function downloadAttachment(Task $task)
    {
        $student = request()->user();

        if (!$this->canAccessTask($task, $student->id)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$task->file_path) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($task->file_path), $task->file_name);
    }
}
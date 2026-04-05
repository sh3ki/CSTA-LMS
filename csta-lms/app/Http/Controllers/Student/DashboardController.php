<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        $classIds = SchoolClass::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })->pluck('id');

        $subjectIds = Subject::whereIn('class_id', $classIds)->pluck('id');
        $submittedTaskIds = Submission::where('student_id', $student->id)->pluck('task_id');

        $taskQuery = Task::whereIn('subject_id', $subjectIds);

        $stats = [
            'classes'   => $classIds->count(),
            'subjects'  => $subjectIds->count(),
            'resources' => Resource::whereIn('subject_id', $subjectIds)->count(),
            'tasks'     => (clone $taskQuery)->count(),
            'submitted' => $submittedTaskIds->count(),
            'pending'   => (clone $taskQuery)
                ->where('due_date', '>', now())
                ->whereNotIn('id', $submittedTaskIds)
                ->count(),
        ];

        $recentTasks = Task::with('subject.schoolClass')
            ->whereIn('subject_id', $subjectIds)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentTasks->each(function ($task) use ($student) {
            $task->student_submission = Submission::where('student_id', $student->id)
                ->where('task_id', $task->id)
                ->first();
        });

        return view('student.dashboard', compact('stats', 'recentTasks'));
    }
}

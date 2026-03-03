<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher  = auth()->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');
        $subjectIds = Subject::whereIn('class_id', $classIds)->pluck('id');

        $stats = [
            'classes'   => $classIds->count(),
            'subjects'  => $subjectIds->count(),
            'students'  => SchoolClass::whereIn('id', $classIds)->withCount('students')->get()->sum('students_count'),
            'resources' => Resource::whereIn('subject_id', $subjectIds)->count(),
            'tasks'     => Task::whereIn('subject_id', $subjectIds)->count(),
            'pending'   => Task::whereIn('subject_id', $subjectIds)->where('due_date', '>', now())->count(),
        ];

        $recentTasks = Task::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recentTasks'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // â”€â”€â”€ Overall Stats â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $totalStudents = User::where('role', 'student')->count();
        $totalTasks    = Task::count();
        $totalSubs     = Submission::whereNotNull('submitted_at')->count();
        $graded        = Submission::whereNotNull('grade')->count();
        $avgGrade       = Submission::whereNotNull('grade')->avg('grade');

        // â”€â”€â”€ Submission Rate â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $possibleSubs  = $totalTasks * $totalStudents;
        $submissionRate = $possibleSubs > 0 ? round(($totalSubs / $possibleSubs) * 100, 1) : 0;

        // â”€â”€â”€ On-time vs Late â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $onTime = Submission::whereNotNull('submitted_at')
            ->whereHas('task', fn($q) => $q->whereColumn('submissions.submitted_at', '<=', 'tasks.due_date'))
            ->count();
        $late = $totalSubs - $onTime;

        // â”€â”€â”€ Grade Distribution â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $gradeRanges = [
            '90-100' => Submission::whereNotNull('grade')->whereBetween('grade', [90, 100])->count(),
            '80-89'  => Submission::whereNotNull('grade')->whereBetween('grade', [80, 89])->count(),
            '70-79'  => Submission::whereNotNull('grade')->whereBetween('grade', [70, 79])->count(),
            '60-69'  => Submission::whereNotNull('grade')->whereBetween('grade', [60, 69])->count(),
            'Below 60' => Submission::whereNotNull('grade')->where('grade', '<', 60)->count(),
        ];

        // â”€â”€â”€ Monthly Submissions (last 6 months) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyCounts[] = Submission::whereNotNull('submitted_at')
                ->whereYear('submitted_at', $month->year)
                ->whereMonth('submitted_at', $month->month)
                ->count();
        }

        // â”€â”€â”€ Class Performance â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $allClasses = SchoolClass::with('students', 'teacher')->orderBy('name')->get();
        $classPerformance = [];
        foreach ($allClasses as $class) {
            $subjectIds = Subject::where('class_id', $class->id)->pluck('id');
            $taskIds    = Task::whereIn('subject_id', $subjectIds)->pluck('id');
            $studentIds = $class->students->pluck('id');

            if ($taskIds->isEmpty() || $studentIds->isEmpty()) continue;

            $avgClassGrade = Submission::whereIn('task_id', $taskIds)
                ->whereIn('student_id', $studentIds)
                ->whereNotNull('grade')
                ->avg('grade');

            $submittedCount = Submission::whereIn('task_id', $taskIds)
                ->whereIn('student_id', $studentIds)
                ->whereNotNull('submitted_at')
                ->count();

            $classPerformance[] = [
                'name'        => $class->name,
                'teacher'     => $class->teacher->full_name ?? 'â€”',
                'students'    => $class->students->count(),
                'avg_grade'   => round($avgClassGrade ?? 0, 1),
                'submissions' => $submittedCount,
            ];
        }
        usort($classPerformance, fn($a, $b) => $b['avg_grade'] <=> $a['avg_grade']);

        // â”€â”€â”€ Subject Performance â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $subjectPerformance = Subject::with(['tasks.submissions', 'schoolClass'])->get()->map(function ($subject) {
            $taskIds = $subject->tasks->pluck('id');
            if ($taskIds->isEmpty()) return null;
            $avg = Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->avg('grade');
            $subs = Submission::whereIn('task_id', $taskIds)->whereNotNull('submitted_at')->count();
            return [
                'name'        => $subject->name,
                'class'       => $subject->schoolClass->name ?? 'â€”',
                'tasks'       => $subject->tasks->count(),
                'submissions' => $subs,
                'avg_grade'   => round($avg ?? 0, 1),
            ];
        })->filter()->sortByDesc('avg_grade')->take(10)->values();

        // â”€â”€â”€ Top Students â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $topStudents = User::where('role', 'student')
            ->with(['submissions' => fn($q) => $q->whereNotNull('grade')])
            ->get()
            ->map(function ($student) {
                $grades = $student->submissions->pluck('grade')->filter();
                return [
                    'id'         => $student->id,
                    'name'       => $student->full_name,
                    'id_number'  => $student->id_number,
                    'avg_grade'  => $grades->isNotEmpty() ? round($grades->avg(), 1) : null,
                    'submitted'  => $student->submissions->count(),
                ];
            })
            ->filter(fn($s) => $s['avg_grade'] !== null)
            ->sortByDesc('avg_grade')
            ->take(10)
            ->values();

        // â”€â”€â”€ Teacher Activity â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $allTeachers = User::where('role', 'teacher')->orderBy('full_name')->get();
        $teacherActivity = $allTeachers->map(function ($teacher) {
            $classIds   = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');
            $subjectIds = Subject::whereIn('class_id', $classIds)->pluck('id');
            $taskCount  = Task::whereIn('subject_id', $subjectIds)->count();
            $resourceCount = Resource::whereIn('subject_id', $subjectIds)->where('uploaded_by', $teacher->id)->count();
            $studentCount = DB::table('class_student')->whereIn('class_id', $classIds)->count();
            return [
                'id'        => $teacher->id,
                'name'      => $teacher->full_name,
                'classes'   => $classIds->count(),
                'students'  => $studentCount,
                'tasks'     => $taskCount,
                'resources' => $resourceCount,
            ];
        })->sortByDesc('tasks')->take(10)->values();

        return view('admin.analytics.index', compact(
            'totalStudents', 'totalTasks', 'totalSubs', 'graded', 'avgGrade',
            'submissionRate', 'onTime', 'late',
            'gradeRanges', 'monthlyLabels', 'monthlyCounts',
            'classPerformance', 'subjectPerformance',
            'topStudents', 'teacherActivity',
            'allClasses', 'allTeachers'
        ));
    }

    public function student(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $submissions = Submission::where('student_id', $student->id)
            ->with(['task.subject'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->get();

        $graded   = $submissions->whereNotNull('grade');
        $avgGrade = $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : null;

        $gradeRanges = [
            '90-100'   => $graded->whereBetween('grade', [90, 100])->count(),
            '80-89'    => $graded->whereBetween('grade', [80, 89])->count(),
            '70-79'    => $graded->whereBetween('grade', [70, 79])->count(),
            '60-69'    => $graded->whereBetween('grade', [60, 69])->count(),
            'Below 60' => $graded->filter(fn($s) => $s->grade < 60)->count(),
        ];

        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyCounts[] = $submissions
                ->filter(fn($s) => $s->submitted_at->year == $month->year && $s->submitted_at->month == $month->month)
                ->count();
        }

        $subjectIds = $submissions->map(fn($s) => $s->task?->subject_id)->filter()->unique();
        $subjects   = Subject::whereIn('id', $subjectIds)->get();

        $subjectPerformance = $subjects->map(function ($subject) use ($submissions) {
            $subs   = $submissions->filter(fn($s) => $s->task && $s->task->subject_id == $subject->id);
            $graded = $subs->whereNotNull('grade');
            if ($subs->isEmpty()) return null;
            return [
                'name'      => $subject->name,
                'count'     => $subs->count(),
                'avg_grade' => $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : 'â€”',
                'highest'   => $graded->isNotEmpty() ? $graded->max('grade') : 'â€”',
                'lowest'    => $graded->isNotEmpty() ? $graded->min('grade') : 'â€”',
            ];
        })->filter()->values();

        $onTime = $submissions->filter(fn($s) => $s->task && $s->task->due_date && $s->submitted_at <= $s->task->due_date)->count();
        $late   = $submissions->count() - $onTime;

        return view('admin.analytics.student', compact(
            'student', 'submissions', 'graded', 'avgGrade',
            'gradeRanges', 'monthlyLabels', 'monthlyCounts',
            'subjectPerformance', 'onTime', 'late'
        ));
    }

    public function teacher(Request $request, User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

        $classes    = SchoolClass::where('teacher_id', $teacher->id)->with('students')->orderBy('name')->get();
        $classIds   = $classes->pluck('id');
        $subjects   = Subject::whereIn('class_id', $classIds)->orderBy('name')->get();
        $subjectIds = $subjects->pluck('id');
        $taskIds    = Task::whereIn('subject_id', $subjectIds)->pluck('id');
        $studentIds = $classes->flatMap(fn($c) => $c->students)->pluck('id')->unique();

        $totalClasses   = $classes->count();
        $totalStudents  = $studentIds->count();
        $totalTasks     = $taskIds->count();
        $resourcesCount = Resource::whereIn('subject_id', $subjectIds)
            ->where('uploaded_by', $teacher->id)
            ->count();

        $avgGrade = Submission::whereIn('task_id', $taskIds)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('grade')
            ->avg('grade');

        $gradeRanges = [
            '90-100'   => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [90, 100])->count(),
            '80-89'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [80, 89])->count(),
            '70-79'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [70, 79])->count(),
            '60-69'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [60, 69])->count(),
            'Below 60' => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->where('grade', '<', 60)->count(),
        ];

        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyCounts[] = Task::whereIn('subject_id', $subjectIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $classBreakdown = $classes->map(function ($class) use ($subjects) {
            $classSubjectIds = $subjects->where('class_id', $class->id)->pluck('id');
            $tIds       = Task::whereIn('subject_id', $classSubjectIds)->pluck('id');
            $studentIds = $class->students->pluck('id');
            $avg        = $tIds->isNotEmpty() && $studentIds->isNotEmpty()
                ? Submission::whereIn('task_id', $tIds)->whereIn('student_id', $studentIds)->whereNotNull('grade')->avg('grade')
                : null;
            return [
                'name'      => $class->name,
                'students'  => $class->students->count(),
                'tasks'     => $tIds->count(),
                'avg_grade' => round($avg ?? 0, 1),
            ];
        });

        $subjectPerformance = $subjects->map(function ($subject) use ($studentIds) {
            $tIds   = Task::where('subject_id', $subject->id)->pluck('id');
            if ($tIds->isEmpty()) return null;
            $avg  = Submission::whereIn('task_id', $tIds)->whereIn('student_id', $studentIds)->whereNotNull('grade')->avg('grade');
            $subs = Submission::whereIn('task_id', $tIds)->whereNotNull('submitted_at')->count();
            return [
                'name'      => $subject->name,
                'tasks'     => $tIds->count(),
                'submissions' => $subs,
                'avg_grade' => round($avg ?? 0, 1),
            ];
        })->filter()->sortByDesc('avg_grade')->values();

        return view('admin.analytics.teacher', compact(
            'teacher', 'classes', 'totalClasses', 'totalStudents', 'totalTasks', 'resourcesCount',
            'avgGrade', 'gradeRanges', 'monthlyLabels', 'monthlyCounts',
            'classBreakdown', 'subjectPerformance'
        ));
    }
}


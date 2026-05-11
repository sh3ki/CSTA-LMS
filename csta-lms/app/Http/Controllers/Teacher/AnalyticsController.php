<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $teacher  = auth()->user();
        $classes  = SchoolClass::where('teacher_id', $teacher->id)->with('students')->orderBy('name')->get();
        $classIds = $classes->pluck('id');

        // ─── Class filter ─────────────────────────────────────────────────────
        $selectedClassId = null;
        if ($request->filled('class_id') && $classes->contains('id', $request->input('class_id'))) {
            $selectedClassId = (int) $request->input('class_id');
        }

        $filteredClassIds   = $selectedClassId ? collect([$selectedClassId]) : $classIds;
        $subjects           = Subject::whereIn('class_id', $filteredClassIds)->orderBy('name')->get();
        $taskIds            = Task::whereIn('subject_id', $subjects->pluck('id'))->pluck('id');
        $filteredStudentIds = $selectedClassId
            ? ($classes->firstWhere('id', $selectedClassId)?->students?->pluck('id') ?? collect())
            : $classes->flatMap(fn($c) => $c->students)->pluck('id')->unique();

        // ─── Overview stats ───────────────────────────────────────────────────
        $totalStudents    = $filteredStudentIds->count();
        $totalTasks       = $taskIds->count();
        $totalSubmissions = Submission::whereIn('task_id', $taskIds)->whereNotNull('submitted_at')->count();
        $graded           = Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->count();
        $avgGrade         = Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->avg('grade');

        // ─── Grade distribution ───────────────────────────────────────────────
        $gradeRanges = [
            '90-100'   => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [90, 100])->count(),
            '80-89'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [80, 89])->count(),
            '70-79'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [70, 79])->count(),
            '60-69'    => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->whereBetween('grade', [60, 69])->count(),
            'Below 60' => Submission::whereIn('task_id', $taskIds)->whereNotNull('grade')->where('grade', '<', 60)->count(),
        ];

        // ─── Monthly submissions (last 6 months) ─────────────────────────────
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyCounts[] = Submission::whereIn('task_id', $taskIds)
                ->whereNotNull('submitted_at')
                ->whereYear('submitted_at', $month->year)
                ->whereMonth('submitted_at', $month->month)
                ->count();
        }

        // ─── Per-class performance ────────────────────────────────────────────
        $classPerformance = [];
        foreach ($classes as $class) {
            $subjectIds = $subjects->where('class_id', $class->id)->pluck('id');
            $tIds       = Task::whereIn('subject_id', $subjectIds)->pluck('id');
            $studentIds = $class->students->pluck('id');

            if ($tIds->isEmpty() || $studentIds->isEmpty()) continue;

            $avg = Submission::whereIn('task_id', $tIds)->whereIn('student_id', $studentIds)
                ->whereNotNull('grade')->avg('grade');
            $subs = Submission::whereIn('task_id', $tIds)->whereIn('student_id', $studentIds)
                ->whereNotNull('submitted_at')->count();

            $classPerformance[] = [
                'name'        => $class->name,
                'students'    => $studentIds->count(),
                'avg_grade'   => round($avg ?? 0, 1),
                'submissions' => $subs,
            ];
        }
        usort($classPerformance, fn($a, $b) => $b['avg_grade'] <=> $a['avg_grade']);

        // ─── Per-subject performance ──────────────────────────────────────────
        $subjectPerformance = $subjects->map(function ($subject) {
            $tIds = Task::where('subject_id', $subject->id)->pluck('id');
            if ($tIds->isEmpty()) return null;
            $avg  = Submission::whereIn('task_id', $tIds)->whereNotNull('grade')->avg('grade');
            $subs = Submission::whereIn('task_id', $tIds)->whereNotNull('submitted_at')->count();
            return [
                'name'        => $subject->name,
                'tasks'       => $tIds->count(),
                'submissions' => $subs,
                'avg_grade'   => round($avg ?? 0, 1),
            ];
        })->filter()->sortByDesc('avg_grade')->values();

        // ─── Top students ─────────────────────────────────────────────────────
        $allStudentIds = $filteredStudentIds;
        $topStudents   = \App\Models\User::whereIn('id', $allStudentIds)
            ->with(['submissions' => fn($q) => $q->whereIn('task_id', $taskIds)->whereNotNull('grade')])
            ->get()
            ->map(function ($student) {
                $grades = $student->submissions->pluck('grade')->filter();
                return [
                    'id'        => $student->id,
                    'name'      => $student->full_name,
                    'id_number' => $student->id_number,
                    'avg_grade' => $grades->isNotEmpty() ? round($grades->avg(), 1) : null,
                    'submitted' => $student->submissions->count(),
                ];
            })
            ->filter(fn($s) => $s['avg_grade'] !== null)
            ->sortByDesc('avg_grade')
            ->take(10)
            ->values();

        return view('teacher.analytics.index', compact(
            'classes', 'subjects', 'selectedClassId',
            'totalStudents', 'totalTasks', 'totalSubmissions', 'graded', 'avgGrade',
            'gradeRanges', 'monthlyLabels', 'monthlyCounts',
            'classPerformance', 'subjectPerformance', 'topStudents'
        ));
    }

    public function student(Request $request, \App\Models\User $student)
    {
        $teacher  = auth()->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        // Confirm student belongs to one of this teacher's classes
        $enrolled = \DB::table('class_student')
            ->whereIn('class_id', $classIds)
            ->where('student_id', $student->id)
            ->exists();
        abort_unless($enrolled, 403);

        $subjects  = Subject::whereIn('class_id', $classIds)->orderBy('name')->get();
        $taskIds   = Task::whereIn('subject_id', $subjects->pluck('id'))->pluck('id');

        $submissions = Submission::where('student_id', $student->id)
            ->whereIn('task_id', $taskIds)
            ->with(['task.subject'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->get();

        $graded = $submissions->whereNotNull('grade');
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

        $subjectPerformance = $subjects->map(function ($subject) use ($student, $submissions) {
            $subs = $submissions->filter(fn($s) => $s->task && $s->task->subject_id == $subject->id);
            $graded = $subs->whereNotNull('grade');
            if ($subs->isEmpty()) return null;
            return [
                'name'      => $subject->name,
                'count'     => $subs->count(),
                'avg_grade' => $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : '—',
                'highest'   => $graded->isNotEmpty() ? $graded->max('grade') : '—',
                'lowest'    => $graded->isNotEmpty() ? $graded->min('grade') : '—',
            ];
        })->filter()->values();

        $onTime = $submissions->filter(fn($s) => $s->task && $s->task->due_date && $s->submitted_at <= $s->task->due_date)->count();
        $late   = $submissions->count() - $onTime;

        return view('teacher.analytics.student', compact(
            'student', 'submissions', 'graded', 'avgGrade',
            'gradeRanges', 'monthlyLabels', 'monthlyCounts',
            'subjectPerformance', 'onTime', 'late', 'taskIds'
        ));
    }
}

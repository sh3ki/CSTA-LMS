<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        // Get all submissions with grades
        $submissions = Submission::with(['task.subject.schoolClass'])
            ->where('student_id', $student->id)
            ->get();

        $graded     = $submissions->filter(fn($s) => $s->grade !== null);
        $submitted  = $submissions->filter(fn($s) => $s->submitted_at !== null);
        $avgGrade   = $graded->isNotEmpty() ? round($graded->avg('grade'), 1) : null;

        // On-time vs late
        $onTime = $submitted->filter(fn($s) => $s->submitted_at && $s->task->due_date && $s->submitted_at->lte($s->task->due_date))->count();
        $late   = $submitted->count() - $onTime;

        // Tasks assigned to student (via their class)
        $classIds   = SchoolClass::whereHas('students', fn($q) => $q->where('users.id', $student->id))->pluck('id');
        $subjectIds = Subject::whereIn('class_id', $classIds)->pluck('id');
        $totalTasks = Task::whereIn('subject_id', $subjectIds)->count();

        // Grade distribution
        $gradeRanges = [
            '90-100'   => $graded->filter(fn($s) => $s->grade >= 90)->count(),
            '80-89'    => $graded->filter(fn($s) => $s->grade >= 80 && $s->grade < 90)->count(),
            '70-79'    => $graded->filter(fn($s) => $s->grade >= 70 && $s->grade < 80)->count(),
            '60-69'    => $graded->filter(fn($s) => $s->grade >= 60 && $s->grade < 70)->count(),
            'Below 60' => $graded->filter(fn($s) => $s->grade < 60)->count(),
        ];

        // Per-subject performance
        $subjectPerformance = $graded->groupBy(fn($s) => $s->task->subject_id ?? 0)->map(function ($subs, $subjectId) {
            $first = $subs->first();
            return [
                'name'      => $first->task->subject->name ?? 'Unknown',
                'avg_grade' => round($subs->avg('grade'), 1),
                'count'     => $subs->count(),
                'highest'   => $subs->max('grade'),
                'lowest'    => $subs->min('grade'),
            ];
        })->sortByDesc('avg_grade')->values();

        // Monthly submissions (last 6 months)
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyCounts[] = $submitted->filter(function ($s) use ($month) {
                return $s->submitted_at->year == $month->year && $s->submitted_at->month == $month->month;
            })->count();
        }

        // Recent graded tasks
        $recentGrades = $graded->sortByDesc(fn($s) => $s->task->due_date)->take(10);

        return view('student.analytics.index', compact(
            'submissions', 'graded', 'submitted', 'avgGrade',
            'onTime', 'late', 'totalTasks',
            'gradeRanges', 'subjectPerformance',
            'monthlyLabels', 'monthlyCounts', 'recentGrades'
        ));
    }
}

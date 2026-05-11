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
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Overall stats
        $stats = [
            'teachers'          => User::where('role', 'teacher')->count(),
            'students'          => User::where('role', 'student')->count(),
            'classes'           => SchoolClass::count(),
            'subjects'          => Subject::count(),
            'resources'         => Resource::count(),
            'tasks'             => Task::count(),
            'total_submissions' => Submission::whereNotNull('submitted_at')->count(),
            'graded'            => Submission::whereNotNull('grade')->count(),
            'pending_grades'    => Submission::whereNotNull('submitted_at')->whereNull('grade')->count(),
        ];

        // Submission status breakdown
        $onTime = Submission::whereNotNull('submitted_at')
            ->whereHas('task', fn($q) => $q->whereColumn('submissions.submitted_at', '<=', 'tasks.due_date'))
            ->count();
        $late    = Submission::whereNotNull('submitted_at')
            ->whereHas('task', fn($q) => $q->whereColumn('submissions.submitted_at', '>', 'tasks.due_date'))
            ->count();
        $missing = $stats['total_submissions'] > 0
            ? Task::count() * User::where('role', 'student')->count() - $stats['total_submissions']
            : 0;
        $missing = max($missing, 0);

        $submissionBreakdown = [
            'on_time' => $onTime,
            'late'    => $late,
        ];

        // Classes with most students
        $topClasses = SchoolClass::withCount('students')
            ->orderByDesc('students_count')
            ->limit(8)
            ->get();

        // Subjects with most tasks
        $topSubjects = Subject::withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(8)
            ->get();

        // Monthly submissions (last 6 months)
        $monthlySubmissions = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlySubmissions[] = [
                'label' => $month->format('M Y'),
                'count' => Submission::whereNotNull('submitted_at')
                    ->whereYear('submitted_at', $month->year)
                    ->whereMonth('submitted_at', $month->month)
                    ->count(),
            ];
        }

        // Grade distribution
        $gradeRanges = [
            '90-100' => Submission::whereNotNull('grade')->whereBetween('grade', [90, 100])->count(),
            '80-89'  => Submission::whereNotNull('grade')->whereBetween('grade', [80, 89])->count(),
            '70-79'  => Submission::whereNotNull('grade')->whereBetween('grade', [70, 79])->count(),
            '60-69'  => Submission::whereNotNull('grade')->whereBetween('grade', [60, 69])->count(),
            'Below 60' => Submission::whereNotNull('grade')->where('grade', '<', 60)->count(),
        ];

        // Recent activity (latest submissions)
        $recentSubmissions = Submission::with(['student', 'task.subject'])
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'stats', 'submissionBreakdown', 'topClasses', 'topSubjects',
            'monthlySubmissions', 'gradeRanges', 'recentSubmissions'
        ));
    }

    public function export(Request $request)
    {
        $submissions = Submission::with(['student', 'task.subject.schoolClass'])
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->get();

        $csvRows = [];
        $csvRows[] = ['Student Name', 'Student ID', 'Task', 'Subject', 'Class', 'Submitted At', 'Grade', 'Status'];

        foreach ($submissions as $sub) {
            $status = $sub->submitted_at && $sub->task->due_date && $sub->submitted_at->gt($sub->task->due_date) ? 'Late' : 'On Time';
            $csvRows[] = [
                $sub->student->full_name ?? 'N/A',
                $sub->student->id_number ?? 'N/A',
                $sub->task->title ?? 'N/A',
                $sub->task->subject->name ?? 'N/A',
                $sub->task->subject->schoolClass->name ?? 'N/A',
                $sub->submitted_at->format('Y-m-d H:i:s'),
                $sub->grade ?? 'Pending',
                $status,
            ];
        }

        $filename = 'submissions_report_' . now()->format('Ymd_His') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($csvRows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

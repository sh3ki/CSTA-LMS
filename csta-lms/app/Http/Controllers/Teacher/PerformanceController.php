<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher  = auth()->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');
        $classes  = SchoolClass::where('teacher_id', $teacher->id)->with('students')->orderBy('name')->get();
        $subjects = Subject::whereIn('class_id', $classIds)->orderBy('name')->get();

        // Selected filters
        $selectedClassId   = $request->class_id;
        $selectedSubjectId = $request->subject_id;

        $students   = collect();
        $tasks      = collect();
        $reportData = [];

        if ($selectedClassId) {
            $class = SchoolClass::with('students')->find($selectedClassId);
            if ($class && $class->teacher_id === $teacher->id) {
                $students = $class->students->sortBy('full_name');

                // Get tasks for the selected class/subject
                $taskQuery = Task::whereHas('subject', function ($q) use ($selectedClassId) {
                    $q->where('class_id', $selectedClassId);
                })->with('submissions');

                if ($selectedSubjectId) {
                    $taskQuery->where('subject_id', $selectedSubjectId);
                }

                $tasks = $taskQuery->orderBy('created_at')->get();

                // Build report data: student => [task grades]
                foreach ($students as $student) {
                    $row = [
                        'student' => $student,
                        'grades'  => [],
                        'total'   => 0,
                        'max'     => 0,
                    ];

                    foreach ($tasks as $task) {
                        $submission = $task->submissions->firstWhere('student_id', $student->id);
                        $grade = $submission ? $submission->grade : null;
                        $row['grades'][] = [
                            'grade'      => $grade,
                            'total_pts'  => $task->total_points,
                            'submitted'  => $submission !== null,
                        ];
                        if ($grade !== null) {
                            $row['total'] += $grade;
                        }
                        $row['max'] += $task->total_points;
                    }

                    $reportData[] = $row;
                }
            }
        }

        // Filter subjects by class
        if ($selectedClassId) {
            $subjects = $subjects->where('class_id', $selectedClassId)->values();
        }

        return view('teacher.performance.index', compact(
            'classes', 'subjects', 'tasks', 'reportData',
            'selectedClassId', 'selectedSubjectId'
        ));
    }

    public function export(Request $request)
    {
        $teacher  = auth()->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $selectedClassId   = $request->class_id;
        $selectedSubjectId = $request->subject_id;

        if (!$selectedClassId) {
            return back()->with('error', 'Please select a class to export.');
        }

        $class = SchoolClass::with('students')->find($selectedClassId);
        if (!$class || $class->teacher_id !== $teacher->id) {
            abort(403);
        }

        $students = $class->students->sortBy('full_name');

        $taskQuery = Task::whereHas('subject', fn($q) => $q->where('class_id', $selectedClassId))
            ->with('submissions');
        if ($selectedSubjectId) {
            $taskQuery->where('subject_id', $selectedSubjectId);
        }
        $tasks = $taskQuery->orderBy('created_at')->get();

        // Build CSV
        $header = ['Student Name', 'ID Number'];
        foreach ($tasks as $task) {
            $header[] = $task->title . ' (/' . $task->total_points . ')';
        }
        $header[] = 'Total';
        $header[] = 'Average (%)';

        $csvRows = [$header];

        foreach ($students as $student) {
            $row   = [$student->full_name, $student->id_number];
            $total = 0;
            $max   = 0;
            foreach ($tasks as $task) {
                $submission = $task->submissions->firstWhere('student_id', $student->id);
                $grade = $submission?->grade;
                $row[] = $grade ?? '-';
                if ($grade !== null) { $total += $grade; }
                $max += $task->total_points;
            }
            $row[] = $total;
            $row[] = $max > 0 ? round(($total / $max) * 100, 1) . '%' : '-';
            $csvRows[] = $row;
        }

        $filename = 'performance_' . str_replace(' ', '_', $class->name) . '_' . now()->format('Ymd') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        foreach ($csvRows as $r) { fputcsv($handle, $r); }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

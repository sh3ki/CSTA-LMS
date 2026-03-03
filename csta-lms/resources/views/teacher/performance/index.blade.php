@extends('layouts.teacher')
@section('title', 'Performance Report')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">bar_chart</span>
            Performance Report
        </h1>
        <p class="page-subtitle">View student grades and performance across your classes.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.performance.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <select name="class_id" id="classFilter" class="form-select" style="width:auto;font-size:14px;min-width:200px;" onchange="this.form.submit()">
                <option value="">— Select Class —</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                        {{ $class->name }} ({{ $class->students->count() }} students)
                    </option>
                @endforeach
            </select>
            @if($selectedClassId)
                <select name="subject_id" class="form-select" style="width:auto;font-size:14px;min-width:200px;" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            @endif
            @if($selectedClassId || $selectedSubjectId)
                <a href="{{ route('teacher.performance.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

@if(!$selectedClassId)
    <!-- No Class Selected -->
    <div class="card">
        <div class="card-body text-center py-5">
            <div style="width:80px;height:80px;background:#f1f3f4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <span class="material-icons" style="font-size:40px;color:#dadce0;">bar_chart</span>
            </div>
            <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Select a Class</h5>
            <p style="font-size:14px;color:#5f6368;max-width:400px;margin:0 auto;">
                Choose a class from the dropdown above to view student performance and grades.
            </p>
        </div>
    </div>
@else
    <!-- Summary Cards -->
    @php
        $totalStudents = count($reportData);
        $totalTasks = $tasks->count();
        $allGrades = collect($reportData)->pluck('grades')->flatten(1)->whereNotNull('grade')->pluck('grade');
        $avgGrade = $allGrades->count() > 0 ? round($allGrades->avg(), 1) : 0;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#fff;font-size:22px;">groups</span>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:#202124;">{{ $totalStudents }}</div>
                        <div style="font-size:12px;color:#5f6368;">Students</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:linear-gradient(135deg,#4a6cf7,#8fa8ff);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#fff;font-size:22px;">assignment</span>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:#202124;">{{ $totalTasks }}</div>
                        <div style="font-size:12px;color:#5f6368;">Tasks</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:linear-gradient(135deg,#f9ab00,#fdd663);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#fff;font-size:22px;">trending_up</span>
                    </div>
                    <div>
                        <div style="font-size:22px;font-weight:700;color:#202124;">{{ $avgGrade }}</div>
                        <div style="font-size:12px;color:#5f6368;">Avg. Grade</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:linear-gradient(135deg,#800020,#a3324a);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#fff;font-size:22px;">school</span>
                    </div>
                    <div>
                        @php
                            $passCount = collect($reportData)->filter(function($r) {
                                return $r['max'] > 0 && ($r['total'] / $r['max']) * 100 >= 75;
                            })->count();
                        @endphp
                        <div style="font-size:22px;font-weight:700;color:#202124;">{{ $totalStudents > 0 ? round(($passCount / $totalStudents) * 100) : 0 }}%</div>
                        <div style="font-size:12px;color:#5f6368;">Passing Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <span class="material-icons" style="color:#5f6368;font-size:18px;">leaderboard</span>
            <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Grade Sheet</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        @foreach($tasks as $task)
                            <th style="text-align:center;min-width:100px;">
                                <div style="font-size:11px;">{{ Str::limit($task->title, 15) }}</div>
                                <div style="font-size:10px;font-weight:400;color:#80868b;">{{ $task->total_points }} pts</div>
                            </th>
                        @endforeach
                        <th style="text-align:center;">Total</th>
                        <th style="text-align:center;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $index => $row)
                        @php
                            $pct = $row['max'] > 0 ? round(($row['total'] / $row['max']) * 100, 1) : 0;
                            $pctColor = $pct >= 75 ? '#34a853' : ($pct >= 50 ? '#f9ab00' : '#ea4335');
                        @endphp
                        <tr>
                            <td style="color:#5f6368;width:48px;">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:28px;height:28px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:600;">
                                        {{ strtoupper(substr($row['student']->full_name, 0, 2)) }}
                                    </div>
                                    <span style="font-weight:500;font-size:13px;">{{ $row['student']->full_name }}</span>
                                </div>
                            </td>
                            @foreach($row['grades'] as $g)
                                <td style="text-align:center;">
                                    @if($g['grade'] !== null)
                                        @php
                                            $gPct = ($g['grade'] / $g['total_pts']) * 100;
                                            $gColor = $gPct >= 75 ? '#34a853' : ($gPct >= 50 ? '#f9ab00' : '#ea4335');
                                        @endphp
                                        <span style="font-weight:600;color:{{ $gColor }};font-size:13px;">{{ $g['grade'] }}</span>
                                    @elseif($g['submitted'])
                                        <span style="color:#f9ab00;font-size:11px;">Pending</span>
                                    @else
                                        <span style="color:#dadce0;">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td style="text-align:center;font-weight:600;font-size:13px;">
                                {{ $row['total'] }} <span style="font-weight:400;color:#5f6368;">/{{ $row['max'] }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge rounded-pill" style="background:{{ $pctColor }}15;color:{{ $pctColor }};font-size:12px;font-weight:600;padding:4px 12px;">
                                    {{ $pct }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $tasks->count() }}" class="text-center py-5">
                                <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">bar_chart</span>
                                <div style="color:#5f6368;font-size:15px;">No performance data available.</div>
                                <p style="font-size:13px;color:#80868b;">Create tasks and grade submissions to see the report.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:10px 12px;white-space:nowrap; }
    .table td { padding:10px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
</style>
@endpush

@extends('layouts.student')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">dashboard</span>
            Student Dashboard
        </h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}! Here is your learning overview.</p>
    </div>
    <div style="font-size:13px;color:#5f6368;">
        <span class="material-icons align-middle me-1" style="font-size:16px;">calendar_today</span>
        {{ now()->format('F d, Y') }}
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff4cc;">
                <span class="material-icons" style="color:#f9ab00;">class</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['classes'] }}</div>
                <div class="stat-label">Classes</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;">
                <span class="material-icons" style="color:#34a853;">menu_book</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['subjects'] }}</div>
                <div class="stat-label">Subjects</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8e6;">
                <span class="material-icons" style="color:#ea4335;">assignment</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['tasks'] }}</div>
                <div class="stat-label">Tasks</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8eaf6;">
                <span class="material-icons" style="color:#4a6cf7;">check_circle</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['submitted'] }}</div>
                <div class="stat-label">Submitted</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;">
                <span class="material-icons" style="color:#f9ab00;">schedule</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8fd;">
                <span class="material-icons" style="color:#7e22ce;">folder_open</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['resources'] }}</div>
                <div class="stat-label">Resources</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('student.subjects.index') }}" class="card p-4 text-decoration-none h-100 dashboard-action">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;background:#fff4cc;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-icons" style="color:#f9ab00;font-size:26px;">menu_book</span>
                </div>
                <div>
                    <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;">Subjects Assigned</div>
                    <div style="font-size:13px;color:#5f6368;">Open your classes, resources, and subject tasks</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('student.tasks.index') }}" class="card p-4 text-decoration-none h-100 dashboard-action">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;background:#e8eaf6;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-icons" style="color:#4a6cf7;font-size:26px;">assignment_turned_in</span>
                </div>
                <div>
                    <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;">Tasks &amp; Activities</div>
                    <div style="font-size:13px;color:#5f6368;">Track deadlines, submissions, grades, and feedback</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('student.announcements.index') }}" class="card p-4 text-decoration-none h-100 dashboard-action">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;background:#fce8ec;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-icons" style="color:#800020;font-size:26px;">campaign</span>
                </div>
                <div>
                    <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;">Announcements</div>
                    <div style="font-size:13px;color:#5f6368;">See updates shared by your teachers</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">assignment</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Recent Tasks</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTasks as $task)
                    @php $submission = $task->student_submission; @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,{{ $task->due_date->isPast() ? '#ea4335,#f28b82' : '#4a6cf7,#8fa8ff' }});border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <span class="material-icons" style="color:#fff;font-size:16px;">assignment</span>
                                </div>
                                <div>
                                    <div style="font-weight:500;font-size:14px;">{{ $task->title }}</div>
                                    @if($task->subject?->schoolClass)
                                        <div style="font-size:12px;color:#5f6368;">{{ $task->subject->schoolClass->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#fff4cc;color:#b45309;font-size:12px;padding:4px 10px;">
                                {{ $task->subject->name }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:{{ $task->due_date->isPast() ? '#ea4335' : '#5f6368' }};">
                            {{ $task->due_date->format('M d, Y h:i A') }}
                        </td>
                        <td>
                            @if($submission)
                                <span class="badge rounded-pill" style="background:#e6f4ea;color:#34a853;font-size:12px;">Submitted</span>
                            @elseif($task->due_date->isPast())
                                <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;">Past Due</span>
                            @else
                                <span class="badge rounded-pill" style="background:#fef7e0;color:#f9ab00;font-size:12px;">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($submission && $submission->grade !== null)
                                <span class="badge rounded-pill" style="background:#e8eaf6;color:#4a6cf7;font-size:12px;">{{ number_format($submission->grade, 2) }}</span>
                            @elseif($submission)
                                <span style="font-size:13px;color:#5f6368;">Pending review</span>
                            @else
                                <span style="font-size:13px;color:#5f6368;">-</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('student.tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <span class="material-icons d-block mb-2" style="font-size:40px;color:#dadce0;">assignment</span>
                            <div style="color:#5f6368;font-size:14px;">No assigned tasks yet.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card { display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #e8eaed;border-radius:14px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08);height:100%; }
    .stat-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .stat-icon .material-icons { font-size:22px; }
    .stat-value { font-size:22px;font-weight:700;color:#202124;line-height:1.1; }
    .stat-label { font-size:12px;color:#5f6368; }
    .dashboard-action { transition:transform .15s, box-shadow .15s; }
    .dashboard-action:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.12); }
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:12px 16px;white-space:nowrap; }
    .table td { padding:12px 16px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
</style>
@endpush

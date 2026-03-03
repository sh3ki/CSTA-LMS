@extends('layouts.teacher')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">dashboard</span>
            Teacher Dashboard
        </h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#800020,#a3324a);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">class</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['classes'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Classes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#f9ab00,#fdd663);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">menu_book</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['subjects'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Subjects</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">groups</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['students'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Students</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#4a6cf7,#8fa8ff);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">folder_open</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['resources'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Resources</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#ea4335,#f28b82);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">assignment</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['tasks'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Tasks</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#9c27b0,#ce93d8);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:22px;">schedule</span>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#202124;">{{ $stats['pending'] }}</div>
                    <div style="font-size:12px;color:#5f6368;">Upcoming</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('teacher.subjects.index') }}" class="card p-4 text-center text-decoration-none" style="transition:transform .15s;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:56px;height:56px;background:#fce8ec;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <span class="material-icons" style="color:#800020;font-size:28px;">menu_book</span>
            </div>
            <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;font-size:14px;">Subjects Assigned</div>
            <div style="font-size:12px;color:#5f6368;margin-top:4px;">View your subjects & students</div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('teacher.resources.index') }}" class="card p-4 text-center text-decoration-none" style="transition:transform .15s;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:56px;height:56px;background:#e6f4ea;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <span class="material-icons" style="color:#34a853;font-size:28px;">upload_file</span>
            </div>
            <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;font-size:14px;">Upload Resources</div>
            <div style="font-size:12px;color:#5f6368;margin-top:4px;">Manage learning materials</div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('teacher.tasks.index') }}" class="card p-4 text-center text-decoration-none" style="transition:transform .15s;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:56px;height:56px;background:#e8eaf6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <span class="material-icons" style="color:#4a6cf7;font-size:28px;">assignment</span>
            </div>
            <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;font-size:14px;">Task Management</div>
            <div style="font-size:12px;color:#5f6368;margin-top:4px;">Create & grade assignments</div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('teacher.performance.index') }}" class="card p-4 text-center text-decoration-none" style="transition:transform .15s;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <div style="width:56px;height:56px;background:#fef7e0;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <span class="material-icons" style="color:#f9ab00;font-size:28px;">bar_chart</span>
            </div>
            <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;font-size:14px;">Performance Report</div>
            <div style="font-size:12px;color:#5f6368;margin-top:4px;">View student grades</div>
        </a>
    </div>
</div>

<!-- Recent Tasks -->
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
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTasks as $task)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,{{ $task->due_date->isPast() ? '#ea4335,#f28b82' : '#4a6cf7,#8fa8ff' }});border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <span class="material-icons" style="color:#fff;font-size:16px;">assignment</span>
                                </div>
                                <span style="font-weight:500;font-size:14px;">{{ $task->title }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;padding:4px 10px;">
                                {{ $task->subject->name }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:{{ $task->due_date->isPast() ? '#ea4335' : '#5f6368' }};">
                            {{ $task->due_date->format('M d, Y h:i A') }}
                        </td>
                        <td>
                            @if($task->due_date->isPast())
                                <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;">Past Due</span>
                            @else
                                <span class="badge rounded-pill" style="background:#e6f4ea;color:#34a853;font-size:12px;">Active</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('teacher.tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <span class="material-icons d-block mb-2" style="font-size:40px;color:#dadce0;">assignment</span>
                            <div style="color:#5f6368;font-size:14px;">No tasks yet. Create your first task!</div>
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
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:12px 16px;white-space:nowrap; }
    .table td { padding:12px 16px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
</style>
@endpush

@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}!</p>
    </div>
    <div style="font-size:13px;color:#5f6368;">
        <span class="material-icons align-middle me-1" style="font-size:16px;">calendar_today</span>
        {{ now()->format('F d, Y') }}
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8ec;">
                <span class="material-icons" style="color:#800020;">person_outline</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['teachers'] }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;">
                <span class="material-icons" style="color:#34a853;">school</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['students'] }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8e6;">
                <span class="material-icons" style="color:#ea4335;">class</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['classes'] }}</div>
                <div class="stat-label">Total Classes</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;">
                <span class="material-icons" style="color:#f9ab00;">menu_book</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['subjects'] }}</div>
                <div class="stat-label">Total Subjects</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;">
                <span class="material-icons" style="color:#1a73e8;">folder_open</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['resources'] }}</div>
                <div class="stat-label">Total Resources</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8fd;">
                <span class="material-icons" style="color:#9334e6;">assignment</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['tasks'] }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;">
                <span class="material-icons" style="color:#0d652d;">check_circle</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['submissions'] }}</div>
                <div class="stat-label">Submissions</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;">
                <span class="material-icons" style="color:#e65100;">pending</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending Grades</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <span class="material-icons me-2" style="color:#5f6368;font-size:18px;">bolt</span>
                <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Quick Actions</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <a href="{{ route('admin.teachers.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#fce8ec'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#800020;font-size:22px;">person_add</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Manage Teachers</div>
                                <div style="font-size:12px;color:#5f6368;">Add or edit teachers</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.students.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#e6f4ea'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#e6f4ea;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#34a853;font-size:22px;">group_add</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Manage Students</div>
                                <div style="font-size:12px;color:#5f6368;">Add or edit students</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.classes.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#fce8e6'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#fce8e6;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#ea4335;font-size:22px;">class</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Manage Classes</div>
                                <div style="font-size:12px;color:#5f6368;">Create & organize classes</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.announcements.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#e8f0fe'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#1a73e8;font-size:22px;">campaign</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Announcements</div>
                                <div style="font-size:12px;color:#5f6368;">Post & manage announcements</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.reports.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#fef7e0'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#fef7e0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#f9ab00;font-size:22px;">bar_chart</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Reports</div>
                                <div style="font-size:12px;color:#5f6368;">View & export reports</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('admin.settings.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#f3e8fd'" onmouseout="this.style.background='#f8f9fa'">
                            <div style="width:44px;height:44px;background:#f3e8fd;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#9334e6;font-size:22px;">settings</span>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#202124;">Settings</div>
                                <div style="font-size:12px;color:#5f6368;">Configure the system</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="material-icons me-2" style="color:#5f6368;font-size:18px;">history</span>
                    <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Recent Activity</span>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" style="font-size:12px;color:#1a73e8;text-decoration:none;">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentActivity as $log)
                <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                    <div style="width:36px;height:36px;background:#f8f9fa;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-icons" style="font-size:18px;color:#5f6368;">person</span>
                    </div>
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:13px;color:#202124;font-weight:500;">{{ $log->user->full_name ?? 'System' }}</div>
                        <div style="font-size:12px;color:#5f6368;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $log->description }}</div>
                        <div style="font-size:11px;color:#9aa0a6;">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted" style="font-size:13px;">No activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Registrations -->
@if($recentUsers->isNotEmpty())
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <span class="material-icons me-2" style="color:#5f6368;font-size:18px;">person_add</span>
            <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Recent Registrations</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead><tr><th>Name</th><th>ID Number</th><th>Role</th><th>Registered</th></tr></thead>
                <tbody>
                    @foreach($recentUsers as $u)
                    <tr>
                        <td>{{ $u->full_name }}</td>
                        <td>{{ $u->id_number }}</td>
                        <td><span class="badge" style="background:{{ $u->role === 'teacher' ? '#fce8ec' : '#e6f4ea' }};color:{{ $u->role === 'teacher' ? '#800020' : '#0d652d' }};">{{ ucfirst($u->role) }}</span></td>
                        <td>{{ $u->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

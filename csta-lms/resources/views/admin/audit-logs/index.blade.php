@extends('layouts.admin')
@section('title', 'Audit Logs')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">history</span>
            Audit Logs
        </h1>
        <p class="page-subtitle">Track all system activity and user actions.</p>
    </div>
    <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-outline-secondary rounded-pill px-4 d-flex align-items-center gap-2">
        <span class="material-icons" style="font-size:18px;">download</span>
        Export CSV
    </a>
</div>

@include('partials._toasts')

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search user, action…" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="teacher" @selected(request('role') === 'teacher')>Teacher</option>
                    <option value="student" @selected(request('role') === 'student')>Student</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach($distinctActions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary w-100" style="background:#800020;border-color:#800020;">Go</button>
                @if(request()->hasAny(['search','role','action','date_from','date_to']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">✕</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($logs->isEmpty())
<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:80px;height:80px;background:#fce8ec;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:40px;color:#800020;">history</span>
        </div>
        <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">No Logs Yet</h5>
        <p style="font-size:14px;color:#5f6368;">System activity will be recorded here as users interact with the LMS.</p>
    </div>
</div>
@else
<div class="card" style="overflow:hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
            <thead style="background:#f8f9fa;border-bottom:1px solid #e8eaed;">
                <tr>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">#</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">User</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">Role</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">Action</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">Description</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">IP Address</th>
                    <th class="px-4 py-3 fw-600" style="color:#5f6368;font-weight:600;">Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="px-4 py-3" style="color:#80868b;">{{ $logs->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        @if($log->user)
                            <div style="font-weight:500;color:#202124;">{{ $log->user->full_name }}</div>
                            <div style="font-size:11px;color:#80868b;">{{ $log->user->id_number }}</div>
                        @else
                            <span style="color:#80868b;">System</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($log->role)
                            <span class="badge rounded-pill px-3"
                                style="background:{{ $log->role === 'admin' ? '#fce8ec' : ($log->role === 'teacher' ? '#e6f4ea' : '#fef7e0') }};
                                       color:{{ $log->role === 'admin' ? '#800020' : ($log->role === 'teacher' ? '#34a853' : '#f9ab00') }};
                                       font-size:11px;font-weight:500;">
                                {{ ucfirst($log->role) }}
                            </span>
                        @else
                            <span style="color:#80868b;">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge rounded-pill px-3" style="background:#f1f3f4;color:#202124;font-size:11px;font-weight:500;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3" style="color:#3c4043;max-width:260px;">{{ $log->description ?? '—' }}</td>
                    <td class="px-4 py-3" style="color:#5f6368;font-family:monospace;font-size:12px;">{{ $log->ip_address }}</td>
                    <td class="px-4 py-3" style="color:#5f6368;white-space:nowrap;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
        <div style="font-size:13px;color:#5f6368;">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} entries
        </div>
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endif
@endsection

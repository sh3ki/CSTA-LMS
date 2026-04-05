@extends('layouts.student')
@section('title', 'Tasks')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">assignment_turned_in</span>
            Tasks &amp; Activities
        </h1>
        <p class="page-subtitle">Track deadlines, submissions, grades, and feedback.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('student.tasks.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by task title or description..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('student.tasks.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="subject_id" class="form-select" style="width:auto;font-size:14px;min-width:180px;">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="width:auto;font-size:14px;min-width:150px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'subject_id', 'status']))
                <a href="{{ route('student.tasks.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">assignment</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Assigned Tasks</span>
        <span class="badge rounded-pill ms-1" style="background:#fff4cc;color:#b45309;font-size:12px;">{{ $tasks->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $index => $task)
                    @php $submission = $submissions->get($task->id); @endphp
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $tasks->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;background:linear-gradient(135deg,{{ $task->due_date->isPast() ? '#ea4335,#f28b82' : '#4a6cf7,#8fa8ff' }});border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span class="material-icons" style="color:#fff;font-size:18px;">assignment</span>
                                </div>
                                <div>
                                    <span style="font-weight:500;">{{ $task->title }}</span>
                                    @if($task->description)
                                        <div style="font-size:12px;color:#5f6368;">{{ Str::limit($task->description, 50) }}</div>
                                    @endif
                                    @if($task->file_name)
                                        <div style="font-size:11px;color:#80868b;">
                                            <span class="material-icons align-middle" style="font-size:12px;">attach_file</span>
                                            {{ $task->file_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#fff4cc;color:#b45309;font-size:12px;font-weight:500;padding:5px 12px;">
                                {{ $task->subject->name }}
                            </span>
                        </td>
                        <td>
                            <span style="color:{{ $task->due_date->isPast() ? '#ea4335' : '#34a853' }};font-size:13px;font-weight:500;">
                                {{ $task->due_date->format('M d, Y h:i A') }}
                            </span>
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
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('student.tasks.show', $task) }}" class="btn-icon" title="View Task">
                                    <span class="material-icons" style="color:#4a6cf7;">visibility</span>
                                </a>
                                @if($task->file_path)
                                    <a href="{{ route('student.tasks.download', $task) }}" class="btn-icon" title="Download Attachment">
                                        <span class="material-icons" style="color:#34a853;">download</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">assignment</span>
                            <div style="color:#5f6368;font-size:15px;">No assigned tasks yet.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tasks->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size:13px;color:#5f6368;">
                    Showing {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
                </div>
                {{ $tasks->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .search-bar { display:flex;align-items:center;gap:8px;background:#f1f3f4;border-radius:8px;padding:8px 12px; }
    .search-bar input { border:none;background:transparent;outline:none;font-size:14px;flex:1;color:#202124; }
    .btn-primary { background:#f9ab00;border-color:#f9ab00; color:#fff; }
    .btn-primary:hover { background:#d98d00;border-color:#d98d00; }
    .btn-icon { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#f8f9fa;text-decoration:none;transition:background .15s; }
    .btn-icon:hover { background:#eef1f3; }
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:12px 16px;white-space:nowrap; }
    .table td { padding:12px 16px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
</style>
@endpush
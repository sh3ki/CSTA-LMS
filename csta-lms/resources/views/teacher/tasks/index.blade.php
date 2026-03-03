@extends('layouts.teacher')
@section('title', 'Task Management')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">assignment</span>
            Task Management
        </h1>
        <p class="page-subtitle">Create assignments, set due dates, and manage student submissions.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
            Create Task
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#800020,#a3324a);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:24px;">assignment</span>
                </div>
                <div>
                    <div style="font-size:24px;font-weight:700;color:#202124;">{{ $tasks->total() }}</div>
                    <div style="font-size:13px;color:#5f6368;">Total Tasks</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:24px;">schedule</span>
                </div>
                <div>
                    @php $upcoming = $tasks->where('due_date', '>', now())->count(); @endphp
                    <div style="font-size:24px;font-weight:700;color:#202124;">{{ $upcoming }}</div>
                    <div style="font-size:13px;color:#5f6368;">Upcoming</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#ea4335,#f28b82);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:24px;">event_busy</span>
                </div>
                <div>
                    @php $pastDue = $tasks->where('due_date', '<=', now())->count(); @endphp
                    <div style="font-size:24px;font-weight:700;color:#202124;">{{ $pastDue }}</div>
                    <div style="font-size:13px;color:#5f6368;">Past Due</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#f9ab00,#fdd663);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-icons" style="color:#fff;font-size:24px;">grading</span>
                </div>
                <div>
                    @php $totalSubs = $tasks->sum(fn($t) => $t->submissions->count()); @endphp
                    <div style="font-size:24px;font-weight:700;color:#202124;">{{ $totalSubs }}</div>
                    <div style="font-size:13px;color:#5f6368;">Submissions</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.tasks.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by task title or description..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('teacher.tasks.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="subject_id" class="form-select" style="width:auto;font-size:14px;min-width:200px;">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="width:auto;font-size:14px;min-width:140px;">
                <option value="">All Status</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'subject_id', 'status']))
                <a href="{{ route('teacher.tasks.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Tasks Table -->
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">assignment</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Tasks</span>
        <span class="badge rounded-pill ms-1" style="background:#fce8ec;color:#800020;font-size:12px;">{{ $tasks->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Points</th>
                    <th>Submissions</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $index => $task)
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
                            <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;font-weight:500;padding:5px 12px;">
                                {{ $task->subject->name }}
                            </span>
                        </td>
                        <td>
                            @if($task->due_date->isPast())
                                <span style="color:#ea4335;font-size:13px;font-weight:500;">
                                    {{ $task->due_date->format('M d, Y h:i A') }}
                                </span>
                                <div style="font-size:11px;color:#ea4335;">Past due</div>
                            @else
                                <span style="color:#34a853;font-size:13px;font-weight:500;">
                                    {{ $task->due_date->format('M d, Y h:i A') }}
                                </span>
                                <div style="font-size:11px;color:#5f6368;">{{ $task->due_date->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#e8eaf6;color:#4a6cf7;font-size:12px;">
                                {{ $task->total_points }} pts
                            </span>
                        </td>
                        <td>
                            @php
                                $subCount = $task->submissions->count();
                                $totalStudents = $task->subject->schoolClass ? $task->subject->schoolClass->students->count() : 0;
                            @endphp
                            <span class="badge rounded-pill" style="background:#e6f4ea;color:#34a853;font-size:12px;">
                                {{ $subCount }}/{{ $totalStudents }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('teacher.tasks.show', $task) }}" class="btn-icon" title="View Submissions">
                                    <span class="material-icons" style="color:#4a6cf7;">visibility</span>
                                </a>
                                <button class="btn-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $task->id }}"
                                    data-title="{{ $task->title }}"
                                    data-subject_id="{{ $task->subject_id }}"
                                    data-description="{{ $task->description }}"
                                    data-due_date="{{ $task->due_date->format('Y-m-d\TH:i') }}"
                                    data-total_points="{{ $task->total_points }}"
                                    data-file_name="{{ $task->file_name }}">
                                    <span class="material-icons" style="color:#800020;">edit</span>
                                </button>
                                <button class="btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $task->id }}"
                                    data-title="{{ $task->title }}">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">assignment</span>
                            <div style="color:#5f6368;font-size:15px;">No tasks created yet.</div>
                            <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                                Create First Task
                            </button>
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
                {{ $tasks->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ══ CREATE TASK MODAL ══ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#800020;font-size:20px;">add_circle</span>
                    </div>
                    <h5 class="modal-title">Create Task</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                placeholder="e.g. Midterm Assignment" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Points <span class="text-danger">*</span></label>
                            <input type="number" name="total_points" class="form-control @error('total_points') is-invalid @enderror"
                                value="{{ old('total_points', 100) }}" min="1" max="1000" required>
                            @error('total_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">— Select Subject —</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Task instructions and details...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Attachment <span class="text-muted">(optional)</span></label>
                            <input type="file" name="file" class="form-control">
                            <div class="form-text">Max file size: 20MB</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ EDIT TASK MODAL ══ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#800020;font-size:20px;">edit</span>
                    </div>
                    <h5 class="modal-title">Edit Task</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Points <span class="text-danger">*</span></label>
                            <input type="number" name="total_points" id="edit_total_points" class="form-control" min="1" max="1000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="edit_subject_id" class="form-select" required>
                                <option value="">— Select Subject —</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" id="edit_due_date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Replace Attachment <span class="text-muted">(optional)</span></label>
                            <input type="file" name="file" class="form-control">
                            <div class="form-text">Current file: <span id="edit_file_name" style="font-weight:500;"></span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ DELETE MODAL ══ -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-body text-center p-5">
                <div style="width:64px;height:64px;background:#fce8e6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span class="material-icons" style="font-size:32px;color:#ea4335;">delete_forever</span>
                </div>
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Delete Task?</h5>
                <p id="deleteTaskName" style="font-size:14px;color:#5f6368;margin-bottom:24px;"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .search-bar { display:flex;align-items:center;gap:8px;background:#f1f3f4;border-radius:8px;padding:8px 12px; }
    .search-bar input { border:none;background:transparent;outline:none;font-size:14px;flex:1;color:#202124; }
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:12px 16px;white-space:nowrap; }
    .table td { padding:12px 16px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
    .btn-icon { background:none;border:none;width:36px;height:36px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s; }
    .btn-icon:hover { background:#f1f3f4; }
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
</style>
@endpush

@push('scripts')
<script>
    // ── Edit Modal
    document.getElementById('editModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('editTaskForm').action     = `/teacher/tasks/${btn.dataset.id}`;
        document.getElementById('edit_title').value        = btn.dataset.title;
        document.getElementById('edit_subject_id').value   = btn.dataset.subject_id;
        document.getElementById('edit_description').value  = btn.dataset.description || '';
        document.getElementById('edit_due_date').value     = btn.dataset.due_date;
        document.getElementById('edit_total_points').value = btn.dataset.total_points;
        document.getElementById('edit_file_name').textContent = btn.dataset.file_name || 'None';
    });

    // ── Delete Modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('deleteForm').action = `/teacher/tasks/${btn.dataset.id}`;
        document.getElementById('deleteTaskName').textContent =
            `This will permanently delete the task "${btn.dataset.title}" and all submissions. This action cannot be undone.`;
    });

    @if ($errors->any())
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif
</script>
@endpush

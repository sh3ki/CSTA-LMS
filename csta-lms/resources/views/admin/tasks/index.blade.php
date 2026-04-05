@extends('layouts.admin')
@section('title', 'Task Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Task Management</h1>
        <p class="page-subtitle">Manage all tasks in the system.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
        <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
        Create Task
    </button>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.tasks.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.tasks.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="subject_id" class="form-select" style="max-width:220px;">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
            <select name="task_type" class="form-select" style="max-width:200px;">
                <option value="">All Types</option>
                @foreach(['Activity', 'Quiz', 'Assignment', 'Others'] as $type)
                    <option value="{{ $type }}" {{ request('task_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">Filter</button>
            @if(request()->hasAny(['search', 'subject_id', 'task_type']))
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Task</th><th>Type</th><th>Subject</th><th>Due</th><th>Points</th><th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $index => $task)
                    <tr>
                        <td>{{ $tasks->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight:500;">{{ $task->title }}</div>
                            <div style="font-size:12px;color:#5f6368;">{{ Str::limit($task->description, 50) }}</div>
                        </td>
                        <td>{{ $task->task_type }}</td>
                        <td>{{ $task->subject->name ?? '—' }}</td>
                        <td>{{ $task->due_date?->format('M d, Y h:i A') }}</td>
                        <td>{{ $task->total_points }}</td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                @if($task->file_path)
                                    <a class="btn-icon" href="{{ route('admin.tasks.download', $task) }}"><span class="material-icons" style="color:#34a853;">download</span></a>
                                @endif
                                <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $task->id }}" data-title="{{ $task->title }}" data-task_type="{{ $task->task_type }}" data-subject_id="{{ $task->subject_id }}" data-description="{{ $task->description }}" data-due_date="{{ $task->due_date?->format('Y-m-d\TH:i') }}" data-total_points="{{ $task->total_points }}" data-file_name="{{ $task->file_name }}">
                                    <span class="material-icons" style="color:#800020;">edit</span>
                                </button>
                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon"><span class="material-icons" style="color:#ea4335;">delete_outline</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No tasks found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tasks->hasPages())
        <div class="card-footer bg-white">{{ $tasks->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">@csrf
            <div class="modal-header"><h5 class="modal-title">Create Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Type</label><select name="task_type" class="form-select" required>@foreach(['Activity','Quiz','Assignment','Others'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Due Date</label><input type="datetime-local" name="due_date" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Points</label><input type="number" name="total_points" class="form-control" min="1" max="1000" value="100" required></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">Attachment</label><input type="file" name="file" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Create</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form id="editTaskForm" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label">Title</label><input type="text" name="title" id="edit_title" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Type</label><select name="task_type" id="edit_task_type" class="form-select" required>@foreach(['Activity','Quiz','Assignment','Others'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Subject</label><select name="subject_id" id="edit_subject_id" class="form-select" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Due Date</label><input type="datetime-local" name="due_date" id="edit_due_date" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Points</label><input type="number" name="total_points" id="edit_total_points" class="form-control" min="1" max="1000" required></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="edit_description" class="form-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">Replace Attachment</label><input type="file" name="file" class="form-control"><div class="form-text">Current file: <span id="edit_file_name"></span></div></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div></div>
</div>
@endsection

@push('styles')
<style>
.search-bar{display:flex;align-items:center;gap:8px;background:#f1f3f4;border-radius:8px;padding:8px 12px}
.search-bar input{border:none;background:transparent;outline:none;font-size:14px;flex:1;color:#202124}
.btn-primary{background:#800020;border-color:#800020}.btn-primary:hover{background:#5c0016;border-color:#5c0016}.btn-icon{background:none;border:none;width:34px;height:34px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}.btn-icon:hover{background:#f1f3f4}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    document.getElementById('editTaskForm').action = `/admin/tasks/${btn.dataset.id}`;
    document.getElementById('edit_title').value = btn.dataset.title;
    document.getElementById('edit_task_type').value = btn.dataset.task_type;
    document.getElementById('edit_subject_id').value = btn.dataset.subject_id;
    document.getElementById('edit_due_date').value = btn.dataset.due_date;
    document.getElementById('edit_total_points').value = btn.dataset.total_points;
    document.getElementById('edit_description').value = btn.dataset.description || '';
    document.getElementById('edit_file_name').textContent = btn.dataset.file_name || 'none';
});
</script>
@endpush

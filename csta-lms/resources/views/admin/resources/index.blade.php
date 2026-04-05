@extends('layouts.admin')
@section('title', 'Resources Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Resources Management</h1>
        <p class="page-subtitle">Manage all uploaded resources in the system.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
        <span class="material-icons align-middle me-1" style="font-size:16px;">upload_file</span>
        Upload Resource
    </button>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.resources.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search resources..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.resources.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
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
            <select name="resource_type" class="form-select" style="max-width:220px;">
                <option value="">All Types</option>
                @foreach(['Course Syllabus', 'Lesson', 'Others'] as $type)
                    <option value="{{ $type }}" {{ request('resource_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">Filter</button>
            @if(request()->hasAny(['search', 'subject_id', 'resource_type']))
                <a href="{{ route('admin.resources.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Title</th><th>Type</th><th>Subject</th><th>File</th><th>Uploaded By</th><th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resources as $index => $resource)
                    <tr>
                        <td>{{ $resources->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight:500;">{{ $resource->title }}</div>
                            <div style="font-size:12px;color:#5f6368;">{{ Str::limit($resource->description, 50) }}</div>
                        </td>
                        <td>{{ $resource->resource_type }}</td>
                        <td>{{ $resource->subject->name ?? '—' }}</td>
                        <td>{{ $resource->file_name }}</td>
                        <td>{{ $resource->uploader->full_name ?? '—' }}</td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                <a class="btn-icon" href="{{ route('admin.resources.download', $resource) }}"><span class="material-icons" style="color:#34a853;">download</span></a>
                                <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $resource->id }}" data-title="{{ $resource->title }}" data-resource_type="{{ $resource->resource_type }}" data-subject_id="{{ $resource->subject_id }}" data-description="{{ $resource->description }}" data-file_name="{{ $resource->file_name }}">
                                    <span class="material-icons" style="color:#800020;">edit</span>
                                </button>
                                <form action="{{ route('admin.resources.destroy', $resource) }}" method="POST" onsubmit="return confirm('Delete this resource?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon"><span class="material-icons" style="color:#ea4335;">delete_outline</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No resources found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($resources->hasPages())
        <div class="card-footer bg-white">{{ $resources->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">@csrf
            <div class="modal-header"><h5 class="modal-title">Upload Resource</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Type</label><select name="resource_type" class="form-select" required>@foreach(['Course Syllabus','Lesson','Others'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Subject</label><select name="subject_id" class="form-select" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">File</label><input type="file" name="file" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Upload</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="editResourceForm" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit Resource</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Title</label><input type="text" name="title" id="edit_title" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Type</label><select name="resource_type" id="edit_resource_type" class="form-select" required>@foreach(['Course Syllabus','Lesson','Others'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Subject</label><select name="subject_id" id="edit_subject_id" class="form-select" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="edit_description" class="form-control" rows="3"></textarea></div>
                    <div class="col-12"><label class="form-label">Replace File</label><input type="file" name="file" class="form-control"><div class="form-text">Current file: <span id="edit_file_name"></span></div></div>
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
    document.getElementById('editResourceForm').action = `/admin/resources/${btn.dataset.id}`;
    document.getElementById('edit_title').value = btn.dataset.title;
    document.getElementById('edit_resource_type').value = btn.dataset.resource_type;
    document.getElementById('edit_subject_id').value = btn.dataset.subject_id;
    document.getElementById('edit_description').value = btn.dataset.description || '';
    document.getElementById('edit_file_name').textContent = btn.dataset.file_name || 'none';
});
</script>
@endpush

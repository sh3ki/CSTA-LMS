@extends('layouts.teacher')
@section('title', 'Resources Management')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">folder_open</span>
            Resources Management
        </h1>
        <p class="page-subtitle">Upload and manage learning resources for your subjects.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">upload_file</span>
            Upload Resource
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.resources.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by title, description, or filename..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('teacher.resources.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
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
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'subject_id']))
                <a href="{{ route('teacher.resources.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Resources Table -->
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">folder_open</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Resources</span>
        <span class="badge rounded-pill ms-1" style="background:#fce8ec;color:#800020;font-size:12px;">{{ $resources->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>File</th>
                    <th>Uploaded</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resources as $index => $resource)
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $resources->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $iconMap = [
                                        'pdf' => ['picture_as_pdf', '#ea4335'],
                                        'doc' => ['description', '#4285f4'],
                                        'docx' => ['description', '#4285f4'],
                                        'ppt' => ['slideshow', '#f9ab00'],
                                        'pptx' => ['slideshow', '#f9ab00'],
                                        'xls' => ['table_chart', '#34a853'],
                                        'xlsx' => ['table_chart', '#34a853'],
                                        'jpg' => ['image', '#ea4335'],
                                        'jpeg' => ['image', '#ea4335'],
                                        'png' => ['image', '#ea4335'],
                                    ];
                                    $ext = strtolower($resource->file_type);
                                    $icon = $iconMap[$ext][0] ?? 'insert_drive_file';
                                    $iconColor = $iconMap[$ext][1] ?? '#5f6368';
                                @endphp
                                <div style="width:36px;height:36px;background:{{ $iconColor }}15;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span class="material-icons" style="color:{{ $iconColor }};font-size:18px;">{{ $icon }}</span>
                                </div>
                                <div>
                                    <span style="font-weight:500;">{{ $resource->title }}</span>
                                    @if($resource->description)
                                        <div style="font-size:12px;color:#5f6368;">{{ Str::limit($resource->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#f1f3f4;color:#3c4043;font-size:12px;">{{ $resource->resource_type }}</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;font-weight:500;padding:5px 12px;">
                                {{ $resource->subject->name }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#5f6368;">
                                {{ $resource->file_name }}
                                <div style="font-size:11px;color:#80868b;text-transform:uppercase;">{{ $resource->file_type }}</div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#5f6368;">{{ $resource->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('teacher.resources.download', $resource) }}" class="btn-icon" title="Download">
                                    <span class="material-icons" style="color:#34a853;">download</span>
                                </a>
                                <button class="btn-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $resource->id }}"
                                    data-title="{{ $resource->title }}"
                                    data-resource_type="{{ $resource->resource_type }}"
                                    data-subject_id="{{ $resource->subject_id }}"
                                    data-description="{{ $resource->description }}"
                                    data-file_name="{{ $resource->file_name }}">
                                    <span class="material-icons" style="color:#800020;">edit</span>
                                </button>
                                <button class="btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $resource->id }}"
                                    data-title="{{ $resource->title }}">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">folder_open</span>
                            <div style="color:#5f6368;font-size:15px;">No resources uploaded yet.</div>
                            <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                                Upload First Resource
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($resources->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size:13px;color:#5f6368;">
                    Showing {{ $resources->firstItem() }}–{{ $resources->lastItem() }} of {{ $resources->total() }} resources
                </div>
                {{ $resources->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- ══ UPLOAD RESOURCE MODAL ══ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#800020;font-size:20px;">upload_file</span>
                    </div>
                    <h5 class="modal-title">Upload Resource</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.resources.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                placeholder="e.g. Chapter 1 Notes" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Resource Type <span class="text-danger">*</span></label>
                            <select name="resource_type" class="form-select" required>
                                @foreach(['Course Syllabus', 'Lesson', 'Others'] as $type)
                                    <option value="{{ $type }}" {{ old('resource_type', 'Others') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Brief description of this resource...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            <div class="form-text">Max file size: 500MB. Supported: PDF, DOCX, PPT, XLSX, images, etc.</div>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">upload</span>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ EDIT RESOURCE MODAL ══ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#800020;font-size:20px;">edit</span>
                    </div>
                    <h5 class="modal-title">Edit Resource</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editResourceForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Resource Type <span class="text-danger">*</span></label>
                            <select name="resource_type" id="edit_resource_type" class="form-select" required>
                                @foreach(['Course Syllabus', 'Lesson', 'Others'] as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Replace File <span class="text-muted">(optional)</span></label>
                            <input type="file" name="file" class="form-control">
                            <div class="form-text">Current file: <span id="edit_file_name" style="font-weight:500;"></span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Update
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
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Delete Resource?</h5>
                <p id="deleteResourceName" style="font-size:14px;color:#5f6368;margin-bottom:24px;"></p>
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
        document.getElementById('editResourceForm').action = `/teacher/resources/${btn.dataset.id}`;
        document.getElementById('edit_title').value        = btn.dataset.title;
        document.getElementById('edit_resource_type').value = btn.dataset.resource_type;
        document.getElementById('edit_subject_id').value   = btn.dataset.subject_id;
        document.getElementById('edit_description').value  = btn.dataset.description || '';
        document.getElementById('edit_file_name').textContent = btn.dataset.file_name;
    });

    // ── Delete Modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('deleteForm').action = `/teacher/resources/${btn.dataset.id}`;
        document.getElementById('deleteResourceName').textContent =
            `This will permanently delete the resource "${btn.dataset.title}". This action cannot be undone.`;
    });

    @if ($errors->any())
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif
</script>
@endpush

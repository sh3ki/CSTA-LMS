@extends('layouts.admin')

@section('title', 'Subject Management')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Subject Management</h1>
        <p class="page-subtitle">Manage subjects and assign them to classes.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">upload_file</span>
            Import CSV
        </button>
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
            Add Subject
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by subject name or description..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.subjects.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="class_id" class="form-select" style="width:auto;font-size:14px;min-width:180px;">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'class_id']))
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Subjects Table -->
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">menu_book</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Subjects</span>
        <span class="badge rounded-pill ms-1" style="background:#fef7e0;color:#f9ab00;font-size:12px;">{{ $subjects->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject Name</th>
                    <th>Assigned Class</th>
                    <th>Description</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subjects as $index => $subject)
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $subjects->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;background:linear-gradient(135deg,#f9ab00,#fdd663);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span class="material-icons" style="color:#fff;font-size:18px;">menu_book</span>
                                </div>
                                <span style="font-weight:500;">{{ $subject->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($subject->schoolClass)
                                <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;font-weight:500;padding:5px 12px;">
                                    {{ $subject->schoolClass->name }}
                                </span>
                            @else
                                <span style="color:#dadce0;font-size:13px;">— Not assigned —</span>
                            @endif
                        </td>
                        <td style="color:#5f6368;font-size:13px;max-width:260px;">
                            {{ $subject->description ? Str::limit($subject->description, 80) : '—' }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button class="btn-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $subject->id }}"
                                    data-name="{{ $subject->name }}"
                                    data-class_id="{{ $subject->class_id }}"
                                    data-description="{{ $subject->description }}">
                                    <span class="material-icons" style="color:#1a73e8;">edit</span>
                                </button>

                                <button class="btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $subject->id }}"
                                    data-name="{{ $subject->name }}">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">menu_book</span>
                            <div style="color:#5f6368;font-size:15px;">No subjects found.</div>
                            <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add First Subject
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subjects->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size:13px;color:#5f6368;">
                    Showing {{ $subjects->firstItem() }}–{{ $subjects->lastItem() }} of {{ $subjects->total() }} subjects
                </div>
                {{ $subjects->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ══ ADD SUBJECT MODAL ══ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fef7e0;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#f9ab00;font-size:20px;">add_circle</span>
                    </div>
                    <h5 class="modal-title">Add Subject</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="e.g. Mathematics"
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assigned Class</label>
                            <select name="class_id" class="form-select">
                                <option value="">— No class assigned —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Brief description of this subject...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ EDIT SUBJECT MODAL ══ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fef7e0;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#f9ab00;font-size:20px;">edit</span>
                    </div>
                    <h5 class="modal-title">Edit Subject</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubjectForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assigned Class</label>
                            <select name="class_id" id="edit_class_id" class="form-select">
                                <option value="">— No class assigned —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Update Subject
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
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Delete Subject?</h5>
                <p id="deleteSubjectName" style="font-size:14px;color:#5f6368;margin-bottom:24px;"></p>
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

<!-- ══ IMPORT CSV MODAL ══ -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#e6f4ea;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#34a853;font-size:20px;">upload_file</span>
                    </div>
                    <h5 class="modal-title">Import Subjects via CSV</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subjects.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="p-3 rounded-3 mb-4" style="background:#f8f9fa;border:1px dashed #dadce0;">
                        <div style="font-size:13px;font-weight:600;color:#202124;margin-bottom:8px;">
                            <span class="material-icons align-middle me-1" style="font-size:15px;color:#5f6368;">info</span>
                            CSV Format Requirements
                        </div>
                        <div style="font-size:12px;color:#5f6368;line-height:1.8;">
                            Required column: <code>name</code><br>
                            Optional columns: <code>class_name</code>, <code>description</code><br>
                            <strong>First row must be the header row.</strong>
                        </div>
                    </div>
                    <label class="form-label">Select CSV File <span class="text-danger">*</span></label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">upload</span>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Edit Modal
    document.getElementById('editModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('editSubjectForm').action  = `/admin/subjects/${btn.dataset.id}`;
        document.getElementById('edit_name').value         = btn.dataset.name;
        document.getElementById('edit_class_id').value     = btn.dataset.class_id || '';
        document.getElementById('edit_description').value  = btn.dataset.description || '';
    });

    // ── Delete Modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('deleteForm').action = `/admin/subjects/${btn.dataset.id}`;
        document.getElementById('deleteSubjectName').textContent =
            `This will permanently delete the subject "${btn.dataset.name}". This action cannot be undone.`;
    });

    @if ($errors->any())
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif
</script>
@endpush

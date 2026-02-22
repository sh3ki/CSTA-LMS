@extends('layouts.admin')

@section('title', 'Class Management')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Class Management</h1>
        <p class="page-subtitle">Manage classes, assign teachers and students.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">upload_file</span>
            Import CSV
        </button>
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
            Add Class
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.classes.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by class name..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.classes.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request('search'))
                <a href="{{ route('admin.classes.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Classes Table -->
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">class</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Classes</span>
        <span class="badge rounded-pill ms-1" style="background:#fce8e6;color:#ea4335;font-size:12px;">{{ $classes->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class Name</th>
                    <th>Assigned Teacher</th>
                    <th>Students</th>
                    <th>Subjects</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($classes as $index => $class)
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $classes->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;background:linear-gradient(135deg,#ea4335,#f28b82);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span class="material-icons" style="color:#fff;font-size:18px;">class</span>
                                </div>
                                <span style="font-weight:500;">{{ $class->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($class->teacher)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="width:28px;height:28px;background:linear-gradient(135deg,#1a73e8,#8ab4f8);font-size:11px;">
                                        {{ strtoupper(substr($class->teacher->full_name, 0, 2)) }}
                                    </div>
                                    <span style="font-size:13px;">{{ $class->teacher->full_name }}</span>
                                </div>
                            @else
                                <span style="color:#dadce0;font-size:13px;">— Not assigned —</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#f1f3f4;color:#3c4043;font-size:12px;font-weight:500;">
                                {{ $class->students->count() }} student{{ $class->students->count() !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill" style="background:#f1f3f4;color:#3c4043;font-size:12px;font-weight:500;">
                                {{ $class->subjects->count() }} subject{{ $class->subjects->count() !== 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                {{-- Edit --}}
                                <button class="btn-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $class->id }}"
                                    data-name="{{ $class->name }}"
                                    data-teacher_id="{{ $class->teacher_id }}"
                                    data-students="{{ $class->students->pluck('id')->toJson() }}">
                                    <span class="material-icons" style="color:#1a73e8;">edit</span>
                                </button>

                                {{-- Delete --}}
                                <button class="btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $class->id }}"
                                    data-name="{{ $class->name }}">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">class</span>
                            <div style="color:#5f6368;font-size:15px;">No classes found.</div>
                            <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add First Class
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($classes->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size:13px;color:#5f6368;">
                    Showing {{ $classes->firstItem() }}–{{ $classes->lastItem() }} of {{ $classes->total() }} classes
                </div>
                {{ $classes->links() }}
            </div>
        </div>
    @endif
</div>

<!-- ══ ADD CLASS MODAL ══ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8e6;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#ea4335;font-size:20px;">add_circle</span>
                    </div>
                    <h5 class="modal-title">Add Class</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="e.g. Grade 11 - STEM A"
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">— No teacher assigned —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->full_name }} ({{ $teacher->id_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Students</label>
                            <div style="max-height:200px;overflow-y:auto;border:1.5px solid #dadce0;border-radius:8px;padding:12px;">
                                @if($students->isEmpty())
                                    <p style="font-size:13px;color:#5f6368;margin:0;">No students available. Add students first.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 g-2">
                                        @foreach($students as $student)
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="students[]"
                                                        value="{{ $student->id }}"
                                                        id="add_std_{{ $student->id }}"
                                                        {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="add_std_{{ $student->id }}" style="font-size:13px;">
                                                        {{ $student->full_name }}
                                                        <span style="color:#5f6368;">({{ $student->id_number }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ EDIT CLASS MODAL ══ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8e6;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#ea4335;font-size:20px;">edit</span>
                    </div>
                    <h5 class="modal-title">Edit Class</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClassForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Teacher</label>
                            <select name="teacher_id" id="edit_teacher_id" class="form-select">
                                <option value="">— No teacher assigned —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->full_name }} ({{ $teacher->id_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Students</label>
                            <div id="editStudentsList" style="max-height:200px;overflow-y:auto;border:1.5px solid #dadce0;border-radius:8px;padding:12px;">
                                @if($students->isEmpty())
                                    <p style="font-size:13px;color:#5f6368;margin:0;">No students available.</p>
                                @else
                                    <div class="row row-cols-1 row-cols-md-2 g-2">
                                        @foreach($students as $student)
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input edit-student-check" type="checkbox"
                                                        name="students[]"
                                                        value="{{ $student->id }}"
                                                        id="edit_std_{{ $student->id }}">
                                                    <label class="form-check-label" for="edit_std_{{ $student->id }}" style="font-size:13px;">
                                                        {{ $student->full_name }}
                                                        <span style="color:#5f6368;">({{ $student->id_number }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Update Class
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
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Delete Class?</h5>
                <p id="deleteClassName" style="font-size:14px;color:#5f6368;margin-bottom:24px;"></p>
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
                    <h5 class="modal-title">Import Classes via CSV</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.classes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="p-3 rounded-3 mb-4" style="background:#f8f9fa;border:1px dashed #dadce0;">
                        <div style="font-size:13px;font-weight:600;color:#202124;margin-bottom:8px;">
                            <span class="material-icons align-middle me-1" style="font-size:15px;color:#5f6368;">info</span>
                            CSV Format Requirements
                        </div>
                        <div style="font-size:12px;color:#5f6368;line-height:1.8;">
                            Required column: <code>name</code><br>
                            Optional column: <code>teacher_id_number</code><br>
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
        document.getElementById('editClassForm').action = `/admin/classes/${btn.dataset.id}`;
        document.getElementById('edit_name').value = btn.dataset.name;

        // Teacher select
        const teacherSel = document.getElementById('edit_teacher_id');
        teacherSel.value = btn.dataset.teacher_id || '';

        // Students checkboxes
        const enrolled = JSON.parse(btn.dataset.students || '[]');
        document.querySelectorAll('.edit-student-check').forEach(cb => {
            cb.checked = enrolled.includes(parseInt(cb.value));
        });
    });

    // ── Delete Modal
    document.getElementById('deleteModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('deleteForm').action = `/admin/classes/${btn.dataset.id}`;
        document.getElementById('deleteClassName').textContent =
            `This will permanently delete the class "${btn.dataset.name}". This action cannot be undone.`;
    });

    @if ($errors->any())
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif
</script>
@endpush

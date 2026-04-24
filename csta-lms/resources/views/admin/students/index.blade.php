@extends('layouts.admin')

@section('title', 'Student Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Student Management</h1>
        <p class="page-subtitle">Manage all student accounts in the system.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">upload_file</span>
            Import CSV
        </button>
        <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
            Add Student
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by name, ID, email, or contact..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.students.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="status" class="form-select" style="width:auto;font-size:14px;">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="course" class="form-select" style="width:auto;font-size:14px;min-width:140px;">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course }}" {{ request('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'course']))
                <a href="{{ route('admin.students.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">school</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Students</span>
        <span class="badge rounded-pill ms-1" style="background:#e6f4ea;color:#34a853;font-size:12px;">{{ $students->total() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>ID Number</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $index => $student)
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar" style="width:36px;height:36px;background:linear-gradient(135deg,#34a853,#81c995);font-size:13px;">
                                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                </div>
                                <span style="font-weight:500;">{{ $student->full_name }}</span>
                            </div>
                        </td>
                        <td>
                            <code style="background:#f1f3f4;padding:3px 8px;border-radius:6px;font-size:13px;color:#202124;">{{ $student->id_number }}</code>
                        </td>
                        <td style="color:#5f6368;">{{ $student->email ?: '—' }}</td>
                        <td style="color:#5f6368;">{{ $student->course ?: '—' }}</td>
                        <td style="color:#5f6368;">{{ $student->year_level ?: '—' }}</td>
                        <td style="color:#5f6368;">{{ $student->contact_number ?: '—' }}</td>
                        <td>
                            @if($student->status)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button class="btn-icon" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $student->id }}"
                                    data-full_name="{{ $student->full_name }}"
                                    data-id_number="{{ $student->id_number }}"
                                    data-email="{{ $student->email }}"
                                    data-course="{{ $student->course }}"
                                    data-year_level="{{ $student->year_level }}"
                                    data-contact_number="{{ $student->contact_number }}">
                                    <span class="material-icons" style="color:#800020;">edit</span>
                                </button>

                                <form action="{{ route('admin.students.toggleStatus', $student) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-icon" title="{{ $student->status ? 'Deactivate' : 'Activate' }}"
                                        onclick="return confirm('{{ $student->status ? 'Deactivate' : 'Activate' }} student?')">
                                        <span class="material-icons" style="color:{{ $student->status ? '#f9ab00' : '#34a853' }};">
                                            {{ $student->status ? 'block' : 'check_circle' }}
                                        </span>
                                    </button>
                                </form>

                                <button class="btn-icon" title="Change Password"
                                    data-bs-toggle="modal" data-bs-target="#passwordModal"
                                    data-id="{{ $student->id }}"
                                    data-name="{{ $student->full_name }}">
                                    <span class="material-icons" style="color:#5f6368;">key</span>
                                </button>

                                <button class="btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $student->id }}"
                                    data-name="{{ $student->full_name }}">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">school</span>
                            <div style="color:#5f6368;font-size:15px;">No students found.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
        <div class="card-footer bg-white border-top-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div style="font-size:13px;color:#5f6368;">
                    Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
                </div>
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ID Number <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course <span class="text-danger">*</span></label>
                            <select name="course" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courseOptions as $course)
                                    <option value="{{ $course }}" {{ old('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" class="form-select">
                                <option value="">Select Year Level</option>
                                @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $year)
                                    <option value="{{ $year }}" {{ old('year_level') === $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ID Number <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" id="edit_id_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course <span class="text-danger">*</span></label>
                            <select name="course" id="edit_course" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courseOptions as $course)
                                    <option value="{{ $course }}">{{ $course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" id="edit_year_level" class="form-select">
                                <option value="">Select Year Level</option>
                                @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p id="passwordStudentName" style="font-size:14px;color:#5f6368;margin-bottom:20px;"></p>
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4" style="color:#202124;">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-body text-center p-5">
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Delete Student?</h5>
                <p id="deleteStudentName" style="font-size:14px;color:#5f6368;margin-bottom:24px;"></p>
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

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Import Students via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="p-3 rounded-3 mb-4" style="background:#f8f9fa;border:1px dashed #dadce0;">
                        <div style="font-size:12px;color:#5f6368;line-height:1.8;">
                            Required columns: <code>id_number</code>, <code>full_name</code>, <code>password</code><br>
                            Optional columns: <code>email</code>, <code>contact_number</code>, <code>course</code>, <code>year_level</code><br>
                            First row must be the header row.
                        </div>
                    </div>
                    <label class="form-label">Select CSV File <span class="text-danger">*</span></label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .search-bar { display:flex;align-items:center;gap:8px;background:#f1f3f4;border-radius:8px;padding:8px 12px; }
    .search-bar input { border:none;background:transparent;outline:none;font-size:14px;flex:1;color:#202124; }
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('editModal').addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        document.getElementById('editStudentForm').action = `/admin/students/${btn.dataset.id}`;
        document.getElementById('edit_full_name').value = btn.dataset.full_name || '';
        document.getElementById('edit_id_number').value = btn.dataset.id_number || '';
        document.getElementById('edit_email').value = btn.dataset.email || '';
        document.getElementById('edit_course').value = btn.dataset.course || '';
        const yearSelect = document.getElementById('edit_year_level');
        const yearValue = btn.dataset.year_level || '';
        const hasOption = Array.from(yearSelect.options).some(opt => opt.value === yearValue);
        if (yearValue && !hasOption) {
            const opt = document.createElement('option');
            opt.value = yearValue;
            opt.textContent = yearValue;
            yearSelect.appendChild(opt);
        }
        yearSelect.value = yearValue;
        document.getElementById('edit_contact_number').value = btn.dataset.contact_number || '';
    });

    document.getElementById('passwordModal').addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        document.getElementById('passwordForm').action = `/admin/students/${btn.dataset.id}/password`;
        document.getElementById('passwordStudentName').textContent = `Changing password for: ${btn.dataset.name}`;
    });

    document.getElementById('deleteModal').addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        document.getElementById('deleteForm').action = `/admin/students/${btn.dataset.id}`;
        document.getElementById('deleteStudentName').textContent = `This will permanently delete the student account of "${btn.dataset.name}".`;
    });

    @if ($errors->any())
        new bootstrap.Modal(document.getElementById('addModal')).show();
    @endif
</script>
@endpush

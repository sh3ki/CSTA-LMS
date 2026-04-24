@extends('layouts.teacher')
@section('title', 'Classes')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">class</span>
            Classes
        </h1>
        <p class="page-subtitle">Manage your classes and student members.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addModal">
        <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
        Add Class
    </button>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.classes.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search classes..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('teacher.classes.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-3">Filter</button>
            @if(request('search'))
                <a href="{{ route('teacher.classes.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

@php
    $cardColors = [
        ['#800020', '#a3324a'],
        ['#1a6b3c', '#34a853'],
        ['#1a56db', '#4a6cf7'],
        ['#b45309', '#f9ab00'],
        ['#6d28d9', '#8b5cf6'],
    ];
@endphp

<div class="row g-3">
    @forelse($classList as $index => $schoolClass)
        @php $color = $cardColors[$index % count($cardColors)]; @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 class-card"
                 data-bs-toggle="modal"
                 data-bs-target="#classDetailsModal"
                 data-class-id="{{ $schoolClass->id }}"
                 data-class-name="{{ $schoolClass->name }}"
                 data-student-list='@json($schoolClass->students->map(function ($student) { return ["full_name" => $student->full_name, "id_number" => $student->id_number]; })->values())'
                 style="border:none;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.12);cursor:pointer;">
                <div style="background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});padding:20px 20px 14px;position:relative;min-height:120px;">
                    <div style="position:absolute;top:14px;right:14px;opacity:.16;">
                        <span class="material-icons" style="font-size:60px;color:#fff;">class</span>
                    </div>
                    <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#fff;font-size:16px;margin:0 0 6px;max-width:185px;line-height:1.3;">
                        {{ $schoolClass->name }}
                    </h6>
                    <div style="font-size:12px;color:rgba(255,255,255,.9);">{{ $schoolClass->status ? 'Active' : 'Inactive' }}</div>
                </div>

                <div style="padding:16px 20px;">
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center gap-1">
                            <span class="material-icons" style="font-size:16px;color:#5f6368;">groups</span>
                            <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $schoolClass->students->count() }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="material-icons" style="font-size:16px;color:#5f6368;">menu_book</span>
                            <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $schoolClass->subjects->count() }}</span>
                        </div>
                    </div>
                </div>

                <div style="padding:0 20px 16px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#classDetailsModal"
                                data-class-id="{{ $schoolClass->id }}"
                                data-class-name="{{ $schoolClass->name }}"
                                data-student-list='@json($schoolClass->students->map(function ($student) { return ["full_name" => $student->full_name, "id_number" => $student->id_number]; })->values())'
                                onclick="event.stopPropagation();">
                            View Students
                        </button>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn-icon" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $schoolClass->id }}"
                                data-name="{{ $schoolClass->name }}"
                                data-students="{{ $schoolClass->students->pluck('id')->toJson() }}"
                                onclick="event.stopPropagation();">
                                <span class="material-icons" style="color:#800020;">edit</span>
                            </button>
                            <form action="{{ route('teacher.classes.destroy', $schoolClass) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this class?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon" title="Delete">
                                    <span class="material-icons" style="color:#ea4335;">delete_outline</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">class</span>
                <div style="color:#5f6368;font-size:15px;">No classes yet.</div>
            </div>
        </div>
    @endforelse
</div>

@if($classList->hasPages())
    <div class="mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div style="font-size:13px;color:#5f6368;">
            Showing {{ $classList->firstItem() }}–{{ $classList->lastItem() }} of {{ $classList->total() }} classes
        </div>
        {{ $classList->links('pagination::bootstrap-5') }}
    </div>
@endif

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('teacher.classes.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Add Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <label class="form-label">Assign Students</label>
                    <input type="text" class="form-control mb-2 teacher-student-filter-input" data-target="teacherAddStudentsList" placeholder="Search students for checkbox selection...">
                    <div id="teacherAddStudentsList" class="row g-2" style="max-height:260px;overflow:auto;">
                        @forelse($students as $student)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="students[]" value="{{ $student->id }}" id="add_student_{{ $student->id }}">
                                    <label class="form-check-label" for="add_student_{{ $student->id }}">{{ $student->full_name }} ({{ $student->id_number }})</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div style="font-size:13px;color:#5f6368;">No students available from your currently assigned classes.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editClassForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title">Edit Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <label class="form-label">Assign Students</label>
                    <input type="text" class="form-control mb-2 teacher-student-filter-input" data-target="teacherEditStudentsList" placeholder="Search students for checkbox selection...">
                    <div id="teacherEditStudentsList" class="row g-2" style="max-height:260px;overflow:auto;">
                        @forelse($students as $student)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input edit-student-check" type="checkbox" name="students[]" value="{{ $student->id }}" id="edit_student_{{ $student->id }}">
                                    <label class="form-check-label" for="edit_student_{{ $student->id }}">{{ $student->full_name }} ({{ $student->id_number }})</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div style="font-size:13px;color:#5f6368;">No students available from your currently assigned classes.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="classDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title" id="classDetailsTitle">Class Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="classDetailsStudents" class="d-flex flex-column gap-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <a id="classDetailsViewSubjectsBtn" href="#" class="btn btn-primary rounded-pill px-3">
                    View Subjects
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .search-bar { display:flex;align-items:center;gap:8px;background:#f1f3f4;border-radius:8px;padding:8px 12px; }
    .search-bar input { border:none;background:transparent;outline:none;flex:1;font-size:14px;color:#202124; }
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
    .btn-icon { background:none;border:none;width:34px;height:34px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center; }
    .btn-icon:hover { background:#f1f3f4; }
    .class-card { transition:box-shadow .2s, transform .15s; }
    .class-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.18) !important; transform:translateY(-2px); }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('editClassForm').action = `/teacher/classes/${btn.dataset.id}`;
    document.getElementById('edit_name').value = btn.dataset.name;
    const enrolled = JSON.parse(btn.dataset.students || '[]');
    document.querySelectorAll('.edit-student-check').forEach(cb => {
        cb.checked = enrolled.includes(parseInt(cb.value, 10));
    });
});

document.getElementById('classDetailsModal').addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;
    if (!trigger) return;

    const className = trigger.dataset.className || 'Class Students';
    const classId = trigger.dataset.classId;
    const students = JSON.parse(trigger.dataset.studentList || '[]');

    document.getElementById('classDetailsTitle').textContent = `${className} - Students`;

    const container = document.getElementById('classDetailsStudents');
    if (!students.length) {
        container.innerHTML = '<div class="text-center py-3" style="color:#5f6368;font-size:14px;">No students assigned in this class.</div>';
    } else {
        container.innerHTML = students.map((student) => `
            <div class="d-flex align-items-center justify-content-between" style="background:#f8f9fa;border:1px solid #e8eaed;border-radius:10px;padding:10px 12px;">
                <span style="font-size:14px;color:#202124;font-weight:500;">${student.full_name}</span>
                <span style="font-size:12px;color:#5f6368;">${student.id_number}</span>
            </div>
        `).join('');
    }

    const subjectsBase = '{{ route('teacher.subjects.index') }}';
    document.getElementById('classDetailsViewSubjectsBtn').href = `${subjectsBase}?search=&class_id=${classId}`;
});

document.querySelectorAll('.teacher-student-filter-input').forEach((input) => {
    input.addEventListener('input', () => {
        const needle = input.value.trim().toLowerCase();
        const targetId = input.dataset.target;
        const list = document.getElementById(targetId);
        if (!list) return;

        list.querySelectorAll('.form-check').forEach((check) => {
            const text = check.textContent.toLowerCase();
            check.closest('.col-md-6, .col-12')?.classList.toggle('d-none', needle !== '' && !text.includes(needle));
        });
    });
});
</script>
@endpush

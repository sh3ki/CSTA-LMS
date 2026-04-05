@extends('layouts.teacher')
@section('title', 'Subjects Assigned')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">menu_book</span>
            Subjects Assigned
        </h1>
        <p class="page-subtitle">View your assigned subjects and enrolled students.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
        <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
        Add Subject
    </button>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.subjects.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by subject name or description..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('teacher.subjects.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
                        <span class="material-icons" style="font-size:16px;">close</span>
                    </a>
                @endif
            </div>
            <select name="class_id" class="form-select" style="width:auto;font-size:14px;min-width:180px;">
                <option value="">All Classes</option>
                @foreach($teacherClasses as $teacherClass)
                    <option value="{{ $teacherClass->id }}" {{ request('class_id') == $teacherClass->id ? 'selected' : '' }}>
                        {{ $teacherClass->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-pill px-3">
                <span class="material-icons align-middle" style="font-size:16px;">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'class_id']))
                <a href="{{ route('teacher.subjects.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Subject Cards Grid (Google Classroom Style) -->
@php
    $cardColors = [
        ['#800020', '#a3324a'],
        ['#1a6b3c', '#34a853'],
        ['#1a56db', '#4a6cf7'],
        ['#b45309', '#f9ab00'],
        ['#6d28d9', '#8b5cf6'],
        ['#0e7490', '#06b6d4'],
        ['#be123c', '#f43f5e'],
        ['#4338ca', '#6366f1'],
    ];
@endphp

<div class="row g-3">
    @forelse ($subjects as $index => $subject)
        @php $color = $cardColors[$index % count($cardColors)]; @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('teacher.subjects.show', $subject) }}" class="subject-card" style="text-decoration:none;display:block;">
                <div class="card h-100" style="border:none;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.12);transition:box-shadow .2s,transform .15s;">
                    <!-- Card Banner -->
                    <div style="background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});padding:20px 20px 16px;position:relative;min-height:120px;">
                        <div style="position:absolute;top:16px;right:16px;opacity:.15;">
                            <span class="material-icons" style="font-size:64px;color:#fff;">menu_book</span>
                        </div>
                        <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#fff;font-size:16px;margin:0 0 4px;line-height:1.3;max-width:180px;">
                            {{ $subject->name }}
                        </h6>
                        @if($subject->schoolClass)
                            <div style="font-size:13px;color:rgba(255,255,255,.85);margin-bottom:4px;">
                                {{ $subject->schoolClass->name }}
                            </div>
                        @endif
                        @if($subject->subject_code)
                            <div style="font-size:11px;color:rgba(255,255,255,.9);margin-bottom:3px;">Code: {{ $subject->subject_code }}</div>
                        @endif
                        @if($subject->course_code || $subject->semester)
                            <div style="font-size:11px;color:rgba(255,255,255,.85);margin-bottom:3px;">
                                {{ $subject->course_code ?: 'Course code n/a' }}
                                @if($subject->semester)
                                    &middot; {{ $subject->semester }} sem
                                @endif
                            </div>
                        @endif
                        @if($subject->description)
                            <div style="font-size:12px;color:rgba(255,255,255,.7);line-height:1.4;">
                                {{ Str::limit($subject->description, 50) }}
                            </div>
                        @endif
                    </div>
                    <!-- Card Body -->
                    <div style="padding:16px 20px;flex:1;">
                        <div class="d-flex align-items-center gap-4">
                            <div class="d-flex align-items-center gap-1" title="Students">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">groups</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->schoolClass ? $subject->schoolClass->students->count() : 0 }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1" title="Resources">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">folder_open</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->resources->count() }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1" title="Tasks">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">assignment</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->tasks->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card Footer -->
                    <div style="padding:0 20px 16px;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <span class="material-icons" style="color:#fff;font-size:14px;">person</span>
                            </div>
                            <span style="font-size:12px;color:#80868b;">{{ auth()->user()->full_name }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <span class="material-icons d-block mb-2" style="font-size:64px;color:#dadce0;">menu_book</span>
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">No Subjects Assigned</h5>
                <p style="font-size:14px;color:#5f6368;max-width:400px;margin:0 auto;">
                    Subjects will appear here once the admin assigns classes to you.
                </p>
            </div>
        </div>
    @endforelse
</div>

@if($subjects->hasPages())
    <div class="mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div style="font-size:13px;color:#5f6368;">
            Showing {{ $subjects->firstItem() }}–{{ $subjects->lastItem() }} of {{ $subjects->total() }} subjects
        </div>
        {{ $subjects->links('pagination::bootstrap-5') }}
    </div>
@endif

<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Add Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select class</option>
                                @foreach($teacherClasses as $teacherClass)
                                    <option value="{{ $teacherClass->id }}">{{ $teacherClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="course_code" class="form-control" placeholder="e.g. CS101">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select" required>
                                <option value="">Select semester</option>
                                <option value="1st">1st</option>
                                <option value="2nd">2nd</option>
                                <option value="3rd">3rd</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-3">Create Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="subjectCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Subject Code Generated</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div style="font-size:14px;color:#5f6368;margin-bottom:10px;">
                    {{ session('created_subject_name') }}
                </div>
                <div id="newSubjectCode" style="font-family:'Google Sans',Roboto,sans-serif;font-size:22px;font-weight:700;letter-spacing:1px;color:#800020;margin-bottom:16px;">
                    {{ session('created_subject_code') }}
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="copyNewSubjectCodeBtn">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">content_copy</span>
                    Copy Code
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
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
    .subject-card .card:hover { box-shadow:0 4px 16px rgba(0,0,0,.18) !important;transform:translateY(-2px); }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const copyText = async (text) => {
        if (!text) return;
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            return true;
        }
    };

    const copyBtn = document.getElementById('copyNewSubjectCodeBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            const code = document.getElementById('newSubjectCode')?.textContent?.trim();
            const ok = await copyText(code);
            if (ok) {
                copyBtn.innerHTML = '<span class="material-icons align-middle me-1" style="font-size:16px;">check</span>Copied';
            }
        });
    }

    @if(session('created_subject_code'))
        new bootstrap.Modal(document.getElementById('subjectCodeModal')).show();
    @endif
})();
</script>
@endpush

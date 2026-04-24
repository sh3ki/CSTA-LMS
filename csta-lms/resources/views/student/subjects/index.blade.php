@extends('layouts.student')
@section('title', 'Subjects')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">menu_book</span>
            Subjects Assigned
        </h1>
        <p class="page-subtitle">Open your classes, resources, and subject tasks.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#joinSubjectModal">
        <span class="material-icons align-middle me-1" style="font-size:16px;">group_add</span>
        Join Subject
    </button>
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('student.subjects.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-bar flex-grow-1">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">search</span>
                <input type="text" name="search" placeholder="Search by subject name or description..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('student.subjects.index', request()->except('search', 'page')) }}" style="color:#5f6368;text-decoration:none;">
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
                <a href="{{ route('student.subjects.index') }}" class="btn btn-light rounded-pill px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

@php
    $cardColors = [
        ['#f9ab00', '#fbbc04'],
        ['#800020', '#a3324a'],
        ['#34a853', '#81c995'],
        ['#4a6cf7', '#8fa8ff'],
        ['#ea4335', '#f28b82'],
        ['#9c27b0', '#ce93d8'],
        ['#0ea5e9', '#67e8f9'],
        ['#f97316', '#fdba74'],
    ];
@endphp

<div class="row g-3">
    @forelse ($subjects as $index => $subject)
        @php $color = $cardColors[$index % count($cardColors)]; @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('student.subjects.show', $subject) }}" class="subject-card" style="text-decoration:none;display:block;">
                <div class="card h-100" style="position:relative;border:none;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.12);transition:box-shadow .2s,transform .15s;">
                    @if(($subject->pending_tasks_count ?? 0) > 0)
                        <span style="position:absolute;top:10px;right:10px;z-index:2;background:#ea4335;color:#fff;border-radius:999px;min-width:24px;height:24px;padding:0 8px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                            {{ $subject->pending_tasks_count }}
                        </span>
                    @endif
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
                            <div style="font-size:11px;color:rgba(255,255,255,.9);margin-bottom:4px;">Code: {{ $subject->subject_code }}</div>
                        @endif
                        @if($subject->description)
                            <div style="font-size:12px;color:rgba(255,255,255,.7);line-height:1.4;">
                                {{ Str::limit($subject->description, 60) }}
                            </div>
                        @endif
                    </div>
                    <div style="padding:16px 20px;flex:1;">
                        <div class="d-flex align-items-center gap-4 flex-wrap">
                            <div class="d-flex align-items-center gap-1" title="Resources">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">folder_open</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->resources->count() }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1" title="Tasks">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">assignment</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->tasks->count() }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1" title="Teacher">
                                <span class="material-icons" style="font-size:16px;color:#5f6368;">person</span>
                                <span style="font-size:13px;color:#5f6368;font-weight:500;">{{ $subject->schoolClass?->teacher?->full_name ?? 'Teacher' }}</span>
                            </div>
                        </div>
                    </div>
                    <div style="padding:0 20px 16px;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <span class="material-icons" style="color:#fff;font-size:14px;">open_in_new</span>
                            </div>
                            <span style="font-size:12px;color:#80868b;">Open subject stream</span>
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
                <p style="font-size:14px;color:#5f6368;max-width:420px;margin:0 auto;">
                    Subjects will appear here when your class is assigned by the admin.
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

<div class="modal fade" id="joinSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <h5 class="modal-title">Join Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.subjects.join') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Enter Class Code <span class="text-danger">*</span></label>
                    <input type="text" name="subject_code" class="form-control" placeholder="e.g. CLS-AB12CD34" required>
                    <div class="form-text">Ask your teacher for the subject class code.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-3">Join</button>
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
    .btn-primary { background:#f9ab00;border-color:#f9ab00; color:#fff; }
    .btn-primary:hover { background:#d98d00;border-color:#d98d00; }
    .subject-card .card:hover { box-shadow:0 4px 16px rgba(0,0,0,.18) !important;transform:translateY(-2px); }
</style>
@endpush
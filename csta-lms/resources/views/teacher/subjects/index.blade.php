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
        {{ $subjects->links() }}
    </div>
@endif

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

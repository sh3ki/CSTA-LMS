@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}!</p>
    </div>
    <div style="font-size:13px;color:#5f6368;">
        <span class="material-icons align-middle me-1" style="font-size:16px;">calendar_today</span>
        {{ now()->format('F d, Y') }}
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;">
                <span class="material-icons" style="color:#1a73e8;">person_outline</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['teachers'] }}</div>
                <div class="stat-label">Total Teachers</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;">
                <span class="material-icons" style="color:#34a853;">school</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['students'] }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8e6;">
                <span class="material-icons" style="color:#ea4335;">class</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['classes'] }}</div>
                <div class="stat-label">Total Classes</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;">
                <span class="material-icons" style="color:#f9ab00;">menu_book</span>
            </div>
            <div>
                <div class="stat-value">{{ $stats['subjects'] }}</div>
                <div class="stat-label">Total Subjects</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header d-flex align-items-center">
        <span class="material-icons me-2" style="color:#5f6368;font-size:18px;">bolt</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Quick Actions</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('admin.teachers.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#e8f0fe'" onmouseout="this.style.background='#f8f9fa'">
                    <div style="width:44px;height:44px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-icons" style="color:#1a73e8;font-size:22px;">person_add</span>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500;color:#202124;">Manage Teachers</div>
                        <div style="font-size:12px;color:#5f6368;">Add or edit teachers</div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('admin.students.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#e6f4ea'" onmouseout="this.style.background='#f8f9fa'">
                    <div style="width:44px;height:44px;background:#e6f4ea;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-icons" style="color:#34a853;font-size:22px;">group_add</span>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500;color:#202124;">Manage Students</div>
                        <div style="font-size:12px;color:#5f6368;">Add or edit students</div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('admin.classes.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#fce8e6'" onmouseout="this.style.background='#f8f9fa'">
                    <div style="width:44px;height:44px;background:#fce8e6;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-icons" style="color:#ea4335;font-size:22px;">class</span>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500;color:#202124;">Manage Classes</div>
                        <div style="font-size:12px;color:#5f6368;">Create & organize classes</div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('admin.subjects.index') }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#f8f9fa;transition:all .2s;" onmouseover="this.style.background='#fef7e0'" onmouseout="this.style.background='#f8f9fa'">
                    <div style="width:44px;height:44px;background:#fef7e0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-icons" style="color:#f9ab00;font-size:22px;">menu_book</span>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500;color:#202124;">Manage Subjects</div>
                        <div style="font-size:12px;color:#5f6368;">Add & assign subjects</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

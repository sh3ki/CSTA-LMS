@extends('layouts.teacher')
@section('title', $class->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $class->name }}</h1>
        <p class="page-subtitle">Class members and linked subjects.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('teacher.subjects.index', ['search' => '', 'class_id' => $class->id]) }}" class="btn btn-primary rounded-pill px-3">
            <span class="material-icons align-middle me-1" style="font-size:16px;">menu_book</span>
            View Subjects in this Class
        </a>
        <a href="{{ route('teacher.classes.index') }}" class="btn btn-light rounded-pill px-3">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">Students ({{ $class->students->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($class->students->sortBy('full_name') as $student)
                    <div class="list-group-item d-flex align-items-center gap-3">
                        <div style="width:36px;height:36px;background:#f1f3f4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">
                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:500;">{{ $student->full_name }}</div>
                            <div style="font-size:12px;color:#5f6368;">{{ $student->id_number }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No students assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">Subjects ({{ $class->subjects->count() }})</div>
            <div class="list-group list-group-flush">
                @forelse($class->subjects->sortBy('name') as $subject)
                    <div class="list-group-item">
                        <div style="font-weight:500;">{{ $subject->name }}</div>
                        <div style="font-size:12px;color:#5f6368;">{{ $subject->subject_code ?: 'No code' }}</div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No subjects assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

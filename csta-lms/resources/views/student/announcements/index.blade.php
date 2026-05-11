@extends('layouts.student')
@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">campaign</span>
            Announcements
        </h1>
        <p class="page-subtitle">Updates shared by your teachers and school administration.</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search announcements…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary" style="background:#f9ab00;border-color:#f9ab00;color:#202124;">Search</button>
            @if(request('search'))<a href="{{ route('student.announcements.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>@endif
        </form>
    </div>
</div>

@if($announcements->isEmpty())
<div class="card p-5 text-center">
    <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">campaign</span>
    <p class="text-muted mb-0">No announcements yet. Check back later.</p>
</div>
@else
@foreach($announcements as $ann)
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <div style="width:44px;height:44px;background:#fff4cc;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span class="material-icons" style="color:#f9ab00;">campaign</span>
            </div>
            <div class="flex-grow-1">
                <h6 style="font-weight:600;color:#202124;margin-bottom:4px;">{{ $ann->title }}</h6>
                <p style="font-size:14px;color:#3c4043;margin-bottom:8px;">{{ $ann->body }}</p>
                <div style="font-size:12px;color:#9aa0a6;">
                    <span class="material-icons align-middle me-1" style="font-size:14px;">person</span>{{ $ann->author->full_name ?? 'Administration' }}
                    &nbsp;·&nbsp;
                    <span class="material-icons align-middle me-1" style="font-size:14px;">schedule</span>{{ $ann->published_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="mt-3">{{ $announcements->links() }}</div>
@endif
@endsection
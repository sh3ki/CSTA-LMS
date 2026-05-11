@extends('layouts.teacher')
@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2">campaign</span>
            Announcements
        </h1>
        <p class="page-subtitle">View school announcements and post messages to students.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createModal">
        <span class="material-icons" style="font-size:18px;">add</span>
        Post Announcement
    </button>
</div>

@include('partials._toasts')

<!-- Search -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search announcements…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary" style="background:#800020;border-color:#800020;">Search</button>
            @if(request('search'))<a href="{{ route('teacher.announcements.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>@endif
        </form>
    </div>
</div>

@if($announcements->isEmpty())
<div class="card p-5 text-center">
    <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">campaign</span>
    <p class="text-muted mb-0">No announcements found.</p>
</div>
@else
@foreach($announcements as $ann)
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h6 class="mb-0" style="font-weight:600;color:#202124;">{{ $ann->title }}</h6>
                    @if($ann->isPublished())
                        <span class="badge" style="background:#e6f4ea;color:#0d652d;font-size:11px;">Published</span>
                    @else
                        <span class="badge" style="background:#fef7e0;color:#9c5900;font-size:11px;">Draft</span>
                    @endif
                </div>
                <p style="font-size:14px;color:#3c4043;margin-bottom:8px;">{{ $ann->body }}</p>
                <div style="font-size:12px;color:#9aa0a6;">
                    <span class="material-icons align-middle me-1" style="font-size:14px;">person</span>{{ $ann->author->full_name ?? 'Unknown' }}
                    &nbsp;·&nbsp;
                    <span class="material-icons align-middle me-1" style="font-size:14px;">schedule</span>{{ $ann->created_at->diffForHumans() }}
                </div>
            </div>
            @if($ann->created_by === auth()->id())
            <div class="d-flex gap-1 ms-3">
                <button class="btn btn-sm" style="background:#e8f0fe;color:#1a73e8;border:none;" data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}" title="Edit">
                    <span class="material-icons" style="font-size:16px;">edit</span>
                </button>
                <form action="{{ route('teacher.announcements.destroy', $ann) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm" style="background:#fce8e6;color:#ea4335;border:none;" onclick="return confirm('Delete this announcement?')" title="Delete">
                        <span class="material-icons" style="font-size:16px;">delete</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modal -->
@if($ann->created_by === auth()->id())
<div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.announcements.update', $ann) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $ann->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content</label>
                        <textarea name="body" class="form-control" rows="4" required>{{ $ann->body }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#800020;border-color:#800020;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
<div class="mt-3">{{ $announcements->links() }}</div>
@endif

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Post Announcement to Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Announcement title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="5" required placeholder="Write your message to students…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#800020;border-color:#800020;">Post Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

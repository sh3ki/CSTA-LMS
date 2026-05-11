@extends('layouts.admin')
@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">campaign</span>
            Announcements
        </h1>
        <p class="page-subtitle">Post and manage school announcements.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2" style="background:#800020;border-color:#800020;" data-bs-toggle="modal" data-bs-target="#createModal">
        <span class="material-icons" style="font-size:18px;">add</span>
        New Announcement
    </button>
</div>

@include('partials._toasts')

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title or content…" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="target_role" class="form-select form-select-sm">
                    <option value="">All Audiences</option>
                    <option value="all" @selected(request('target_role') === 'all')>Everyone</option>
                    <option value="teacher" @selected(request('target_role') === 'teacher')>Teachers Only</option>
                    <option value="student" @selected(request('target_role') === 'student')>Students Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100" style="background:#800020;border-color:#800020;">Filter</button>
                @if(request()->hasAny(['search','target_role','status']))
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($announcements->isEmpty())
        <div class="p-5 text-center text-muted">
            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">campaign</span>
            No announcements found.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead style="background:#fce8ec;">
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $ann)
                    <tr>
                        <td>
                            <div style="font-weight:500;color:#202124;">{{ $ann->title }}</div>
                            <div style="font-size:12px;color:#5f6368;max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ann->body }}</div>
                        </td>
                        <td>
                            @php
                                $roleColors = ['all'=>['bg'=>'#e8f0fe','text'=>'#1a73e8'],'teacher'=>['bg'=>'#fce8ec','text'=>'#800020'],'student'=>['bg'=>'#e6f4ea','text'=>'#0d652d']];
                                $rc = $roleColors[$ann->target_role] ?? $roleColors['all'];
                            @endphp
                            <span class="badge" style="background:{{ $rc['bg'] }};color:{{ $rc['text'] }};">{{ ucfirst($ann->target_role) }}</span>
                        </td>
                        <td>
                            @if($ann->isPublished())
                                <span class="badge" style="background:#e6f4ea;color:#0d652d;">Published</span>
                                <div style="font-size:11px;color:#9aa0a6;">{{ $ann->published_at->format('M d, Y') }}</div>
                            @else
                                <span class="badge" style="background:#fef7e0;color:#9c5900;">Draft</span>
                            @endif
                        </td>
                        <td>{{ $ann->author->full_name ?? '—' }}</td>
                        <td>{{ $ann->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                @if(!$ann->isPublished())
                                <form action="{{ route('admin.announcements.publish', $ann) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm" style="background:#e6f4ea;color:#0d652d;border:none;" title="Publish" onclick="return confirm('Publish this announcement?')">
                                        <span class="material-icons" style="font-size:16px;">publish</span>
                                    </button>
                                </form>
                                @endif
                                <button class="btn btn-sm" style="background:#e8f0fe;color:#1a73e8;border:none;" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                                    <span class="material-icons" style="font-size:16px;">edit</span>
                                </button>
                                <form action="{{ route('admin.announcements.destroy', $ann) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm" style="background:#fce8e6;color:#ea4335;border:none;" title="Delete" onclick="return confirm('Delete this announcement?')">
                                        <span class="material-icons" style="font-size:16px;">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $announcements->links() }}</div>
        @endif
    </div>
</div>

<!-- Edit Modals -->
@foreach($announcements as $ann)
<div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.announcements.update', $ann) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $ann->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="5" required>{{ $ann->body }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Audience</label>
                        <select name="target_role" class="form-select">
                            <option value="all" @selected($ann->target_role === 'all')>Everyone</option>
                            <option value="teacher" @selected($ann->target_role === 'teacher')>Teachers Only</option>
                            <option value="student" @selected($ann->target_role === 'student')>Students Only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#800020;border-color:#800020;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div class="modal-header">
                    <h5 class="modal-title">New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               required placeholder="Announcement title"
                               value="{{ old('title') }}">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="body" rows="5"
                                  class="form-control @error('body') is-invalid @enderror"
                                  required placeholder="Write the announcement content…">{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Audience</label>
                            <select name="target_role" class="form-select @error('target_role') is-invalid @enderror">
                                <option value="all" @selected(old('target_role', 'all') === 'all')>Everyone</option>
                                <option value="teacher" @selected(old('target_role') === 'teacher')>Teachers Only</option>
                                <option value="student" @selected(old('target_role') === 'student')>Students Only</option>
                            </select>
                            @error('target_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="publish_now" id="publishNow"
                                       value="1" @checked(old('publish_now', '1') === '1')>
                                <label class="form-check-label" for="publishNow">Publish immediately</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#800020;border-color:#800020;">Create Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any() && old('form_type') === 'create')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('createModal')).show();
    });
</script>
@endif
@endsection

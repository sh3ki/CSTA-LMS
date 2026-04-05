@extends('layouts.teacher')
@section('title', $subject->name)

@section('content')

@php
    $bannerColors = [
        ['#800020', '#a3324a'], ['#1a6b3c', '#34a853'], ['#1a56db', '#4a6cf7'],
        ['#b45309', '#f9ab00'], ['#6d28d9', '#8b5cf6'], ['#0e7490', '#06b6d4'],
    ];
    $color = $bannerColors[$subject->id % count($bannerColors)];
    $students = $subject->schoolClass ? $subject->schoolClass->students : collect();
    // Merge resources + tasks into a single stream sorted by latest first
    $stream = collect();
    foreach ($subject->resources as $r) {
        $stream->push((object)[
            'type' => 'resource', 'item' => $r, 'date' => $r->created_at,
        ]);
    }
    foreach ($subject->tasks as $t) {
        $stream->push((object)[
            'type' => 'task', 'item' => $t, 'date' => $t->created_at,
        ]);
    }
    $stream = $stream->sortByDesc('date');
@endphp

<!-- Subject Banner (Google Classroom Style) -->
<div class="subject-banner" style="background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});border-radius:12px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;min-height:160px;">
    <div style="position:absolute;top:20px;right:24px;opacity:.1;">
        <span class="material-icons" style="font-size:120px;color:#fff;">menu_book</span>
    </div>
    <a href="{{ route('teacher.subjects.index') }}" class="btn btn-sm rounded-pill px-3 mb-2" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(8px);font-size:13px;">
        <span class="material-icons align-middle me-1" style="font-size:14px;">arrow_back</span>
        Back
    </a>
    <h2 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:700;color:#fff;font-size:24px;margin:8px 0 4px;max-width:70%;">{{ $subject->name }}</h2>
    @if($subject->schoolClass)
        <div style="font-size:15px;color:rgba(255,255,255,.9);margin-bottom:2px;">{{ $subject->schoolClass->name }}</div>
    @endif
    @if($subject->description)
        <div style="font-size:13px;color:rgba(255,255,255,.75);max-width:60%;">{{ $subject->description }}</div>
    @endif
    @if($subject->subject_code)
        <div class="d-flex align-items-center gap-2 mt-2">
            <span id="teacherSubjectCode" style="background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;color:#fff;">Code: {{ $subject->subject_code }}</span>
            <button type="button" class="btn btn-sm" id="copyTeacherSubjectCodeBtn" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;">
                <span class="material-icons align-middle" style="font-size:16px;">content_copy</span>
            </button>
        </div>
    @endif
    <div class="d-flex gap-3 mt-3">
        <span style="background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;color:#fff;">
            <span class="material-icons align-middle me-1" style="font-size:14px;">groups</span> {{ $students->count() }} Students
        </span>
        <span style="background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;color:#fff;">
            <span class="material-icons align-middle me-1" style="font-size:14px;">folder_open</span> {{ $subject->resources->count() }} Resources
        </span>
        <span style="background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;color:#fff;">
            <span class="material-icons align-middle me-1" style="font-size:14px;">assignment</span> {{ $subject->tasks->count() }} Tasks
        </span>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="subjectTabs" role="tablist" style="border-bottom:2px solid #e8eaed;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream" type="button" role="tab" style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:14px;color:#800020;">
            <span class="material-icons align-middle me-1" style="font-size:16px;">dynamic_feed</span> Stream
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#studentsTab" type="button" role="tab" style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:14px;">
            <span class="material-icons align-middle me-1" style="font-size:16px;">groups</span> Students ({{ $students->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="subjectTabContent">
    <!-- Stream Tab -->
    <div class="tab-pane fade show active" id="stream" role="tabpanel">
        <!-- Action Buttons -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadResourceModal">
                <span class="material-icons align-middle me-1" style="font-size:18px;">upload_file</span>
                Upload Resource
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                <span class="material-icons align-middle me-1" style="font-size:18px;">assignment_add</span>
                Create Task
            </button>
        </div>

        <!-- Stream Items -->
        @forelse($stream as $entry)
            @if($entry->type === 'resource')
                @php $r = $entry->item; @endphp
                <div class="card mb-3 stream-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#f9ab00,#fdd663);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#fff;font-size:20px;">folder_open</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span style="font-weight:600;font-size:14px;color:#202124;">{{ $r->uploader?->full_name ?? 'Unknown User' }}</span>
                                        <span style="font-size:13px;color:#5f6368;"> posted a new resource</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn-icon" data-bs-toggle="dropdown"><span class="material-icons" style="font-size:18px;color:#5f6368;">more_vert</span></button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('teacher.resources.download', $r) }}">
                                                    <span class="material-icons" style="font-size:16px;">download</span> Download
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('teacher.resources.destroy', $r) }}" method="POST" onsubmit="return confirm('Delete this resource?')">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="redirect_to" value="subject_show">
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <span class="material-icons" style="font-size:16px;">delete</span> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div style="font-size:12px;color:#80868b;margin-bottom:8px;">{{ $r->created_at->format('M d, Y \a\t g:i A') }}</div>
                                <div style="background:#f8f9fa;border-radius:8px;padding:12px 16px;border:1px solid #e8eaed;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @php
                                            $icon = match(true) {
                                                str_contains($r->file_type ?? '', 'pdf') => 'picture_as_pdf',
                                                str_contains($r->file_type ?? '', 'image') => 'image',
                                                str_contains($r->file_type ?? '', 'video') => 'videocam',
                                                str_contains($r->file_type ?? '', 'word') || str_contains($r->file_type ?? '', 'document') => 'description',
                                                str_contains($r->file_type ?? '', 'sheet') || str_contains($r->file_type ?? '', 'excel') => 'table_chart',
                                                str_contains($r->file_type ?? '', 'presentation') || str_contains($r->file_type ?? '', 'powerpoint') => 'slideshow',
                                                default => 'attach_file',
                                            };
                                        @endphp
                                        <span class="material-icons" style="font-size:20px;color:#800020;">{{ $icon }}</span>
                                        <strong style="font-size:14px;color:#202124;">{{ $r->title }}</strong>
                                    </div>
                                    @if($r->description)
                                        <p style="font-size:13px;color:#5f6368;margin:4px 0 0;">{{ $r->description }}</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <a href="{{ route('teacher.resources.download', $r) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                            <span class="material-icons align-middle me-1" style="font-size:14px;">download</span>
                                            {{ $r->file_name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @php $t = $entry->item; @endphp
                <div class="card mb-3 stream-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#4a6cf7,#8fa8ff);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#fff;font-size:20px;">assignment</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span style="font-weight:600;font-size:14px;color:#202124;">{{ $t->creator?->full_name ?? ($subject->schoolClass?->teacher?->full_name ?? 'Unknown User') }}</span>
                                        <span style="font-size:13px;color:#5f6368;"> posted a new assignment:
                                            <strong style="color:#202124;">{{ $t->title }}</strong>
                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn-icon" data-bs-toggle="dropdown"><span class="material-icons" style="font-size:18px;color:#5f6368;">more_vert</span></button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('teacher.tasks.show', $t) }}">
                                                    <span class="material-icons" style="font-size:16px;">visibility</span> View Submissions
                                                </a>
                                            </li>
                                            @if($t->file_path)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('teacher.tasks.download', $t) }}">
                                                    <span class="material-icons" style="font-size:16px;">download</span> Download Attachment
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('teacher.tasks.destroy', $t) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="redirect_to" value="subject_show">
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <span class="material-icons" style="font-size:16px;">delete</span> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div style="font-size:12px;color:#80868b;margin-bottom:8px;">{{ $t->created_at->format('M d, Y \a\t g:i A') }}</div>
                                <div style="background:#f8f9fa;border-radius:8px;padding:12px 16px;border:1px solid #e8eaed;">
                                    @if($t->description)
                                        <p style="font-size:13px;color:#5f6368;margin:0 0 8px;">{{ $t->description }}</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span style="font-size:12px;color:#5f6368;">
                                            <span class="material-icons align-middle me-1" style="font-size:14px;">event</span>
                                            Due: <strong style="color:{{ $t->due_date && $t->due_date->isPast() ? '#ea4335' : '#202124' }};">
                                                {{ $t->due_date ? $t->due_date->format('M d, Y g:i A') : 'No due date' }}
                                            </strong>
                                        </span>
                                        <span style="font-size:12px;color:#5f6368;">
                                            <span class="material-icons align-middle me-1" style="font-size:14px;">grade</span>
                                            {{ $t->total_points }} points
                                        </span>
                                        @if($t->file_path)
                                            <a href="{{ route('teacher.tasks.download', $t) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                                <span class="material-icons align-middle me-1" style="font-size:14px;">attach_file</span>
                                                {{ $t->file_name }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('teacher.tasks.show', $t) }}" class="btn btn-sm rounded-pill px-3" style="background:#800020;color:#fff;font-size:12px;">
                                            View Submissions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="card p-5 text-center">
                <span class="material-icons d-block mb-2" style="font-size:56px;color:#dadce0;">dynamic_feed</span>
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;">No posts yet</h5>
                <p style="font-size:14px;color:#5f6368;max-width:360px;margin:0 auto;">
                    Upload a resource or create a task to get started with this subject.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Students Tab -->
    <div class="tab-pane fade" id="studentsTab" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">groups</span>
                <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Enrolled Students</span>
                <span class="badge rounded-pill ms-1" style="background:#e6f4ea;color:#34a853;font-size:12px;">{{ $students->count() }}</span>
            </div>
            @if($students->count() > 0)
                <div class="p-3">
                    <div class="row g-2">
                        @foreach($students->sortBy('full_name') as $student)
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="d-flex align-items-center gap-3 p-3" style="background:#f8f9fa;border-radius:10px;">
                                    <div style="width:40px;height:40px;background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="color:#fff;font-weight:600;font-size:13px;">{{ strtoupper(substr($student->full_name, 0, 2)) }}</span>
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="font-weight:600;font-size:13px;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->full_name }}</div>
                                        <div style="font-size:12px;color:#5f6368;">{{ $student->id_number }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">groups</span>
                    <div style="color:#5f6368;font-size:14px;">No students enrolled in this class.</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Resource Modal -->
<div class="modal fade" id="uploadResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border:none;border-radius:12px;">
            <form action="{{ route('teacher.resources.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="redirect_to" value="subject_show">
                <div class="modal-header" style="border-bottom:1px solid #e8eaed;padding:20px 24px;">
                    <h5 class="modal-title" style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:18px;">
                        <span class="material-icons align-middle me-2" style="color:#f9ab00;">upload_file</span>
                        Upload Resource
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Chapter 1 Notes" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Resource Type <span class="text-danger">*</span></label>
                        <select name="resource_type" class="form-select" required>
                            <option value="Course Syllabus">Course Syllabus</option>
                            <option value="Lesson">Lesson</option>
                            <option value="Others" selected>Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this resource..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:13px;">File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text" style="font-size:12px;">Max 20MB. Supports PDF, DOC, PPT, images, videos, etc.</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e8eaed;padding:16px 24px;">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">upload</span>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border:none;border-radius:12px;">
            <form action="{{ route('teacher.tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="redirect_to" value="subject_show">
                <div class="modal-header" style="border-bottom:1px solid #e8eaed;padding:20px 24px;">
                    <h5 class="modal-title" style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:18px;">
                        <span class="material-icons align-middle me-2" style="color:#4a6cf7;">assignment_add</span>
                        Create Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Essay on Philippine History" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Task Type <span class="text-danger">*</span></label>
                            <select name="task_type" class="form-select" required>
                                <option value="Activity">Activity</option>
                                <option value="Quiz">Quiz</option>
                                <option value="Assignment" selected>Assignment</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Instructions</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Task instructions and details..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Total Points</label>
                            <input type="number" name="total_points" class="form-control" value="100" min="1" max="1000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Attachment (Optional)</label>
                            <input type="file" name="file" class="form-control">
                            <div class="form-text" style="font-size:12px;">Max 20MB. Attach reference materials or instructions.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e8eaed;padding:16px 24px;">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">add</span>
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
    .btn-outline-primary { color:#800020;border-color:#800020; }
    .btn-outline-primary:hover { background:#800020;color:#fff;border-color:#800020; }
    .nav-tabs .nav-link { color:#5f6368;border:none;padding:10px 20px;border-bottom:3px solid transparent; }
    .nav-tabs .nav-link.active { color:#800020;border-bottom:3px solid #800020;background:transparent; }
    .nav-tabs .nav-link:hover { color:#800020;border-color:transparent; }
    .btn-icon { background:none;border:none;width:32px;height:32px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;padding:0; }
    .btn-icon:hover { background:#f1f3f4; }
    .stream-card { border:1px solid #e8eaed;border-radius:10px;transition:box-shadow .2s; }
    .stream-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.08); }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const btn = document.getElementById('copyTeacherSubjectCodeBtn');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const codeText = document.getElementById('teacherSubjectCode')?.textContent?.replace('Code:', '').trim();
        if (!codeText) return;

        try {
            await navigator.clipboard.writeText(codeText);
        } catch (e) {
            const input = document.createElement('input');
            input.value = codeText;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
        }

        btn.innerHTML = '<span class="material-icons align-middle" style="font-size:16px;">check</span>';
    });
})();
</script>
@endpush

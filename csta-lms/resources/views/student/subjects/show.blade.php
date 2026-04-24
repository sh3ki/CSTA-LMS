@extends('layouts.student')
@section('title', $subject->name)

@section('content')

@php
    $bannerColors = [
        ['#f9ab00', '#fdd663'], ['#fbbc04', '#fce5b3'], ['#f9ab00', '#fbbc04'],
    ];
    $colorIndex = ($subject->id % count($bannerColors));
    $color = $bannerColors[$colorIndex];
@endphp

<!-- Subject Banner (Google Classroom Style) -->
<div class="subject-banner" style="background:linear-gradient(135deg,{{ $color[0] }},{{ $color[1] }});border-radius:12px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;min-height:160px;">
    <div style="position:absolute;top:20px;right:24px;opacity:.1;">
        <span class="material-icons" style="font-size:120px;color:#fff;">menu_book</span>
    </div>
    <a href="{{ route('student.subjects.index') }}" class="btn btn-sm rounded-pill px-3 mb-2" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(8px);font-size:13px;">
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
            <span id="studentSubjectCode" style="background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;color:#fff;">Code: {{ $subject->subject_code }}</span>
            <button type="button" class="btn btn-sm" id="copyStudentSubjectCodeBtn" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;">
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
        <button class="nav-link active" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream" type="button" role="tab" style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:14px;color:#f9ab00;">
            <span class="material-icons align-middle me-1" style="font-size:16px;">dynamic_feed</span> Stream
            @if(($pendingTaskCount ?? 0) > 0)
                <span class="badge rounded-pill ms-1" style="background:#ea4335;color:#fff;font-size:11px;">{{ $pendingTaskCount }}</span>
            @endif
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
        @php
            // Merge resources + tasks into a single stream sorted by latest first
            $stream = collect();
            if (($streamType ?? 'all') !== 'tasks') {
                foreach ($subject->resources as $r) {
                    $stream->push((object)[
                        'type' => 'resource', 'item' => $r, 'date' => $r->created_at,
                    ]);
                }
            }
            if (($streamType ?? 'all') !== 'resources') {
                foreach ($subject->tasks as $t) {
                    $stream->push((object)[
                        'type' => 'task', 'item' => $t, 'date' => $t->created_at,
                    ]);
                }
            }
            $stream = $stream->sortByDesc('date');
        @endphp

        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
            <a href="{{ route('student.subjects.show', ['subject' => $subject->id, 'stream_type' => 'all']) }}"
               class="btn btn-sm rounded-pill px-3 {{ ($streamType ?? 'all') === 'all' ? '' : 'btn-light' }}"
               style="{{ ($streamType ?? 'all') === 'all' ? 'background:#f9ab00;color:#fff;' : '' }}">All</a>
            <a href="{{ route('student.subjects.show', ['subject' => $subject->id, 'stream_type' => 'resources']) }}"
               class="btn btn-sm rounded-pill px-3 {{ ($streamType ?? 'all') === 'resources' ? '' : 'btn-light' }}"
               style="{{ ($streamType ?? 'all') === 'resources' ? 'background:#f9ab00;color:#fff;' : '' }}">Resources Only</a>
            <a href="{{ route('student.subjects.show', ['subject' => $subject->id, 'stream_type' => 'tasks']) }}"
               class="btn btn-sm rounded-pill px-3 {{ ($streamType ?? 'all') === 'tasks' ? '' : 'btn-light' }}"
               style="{{ ($streamType ?? 'all') === 'tasks' ? 'background:#f9ab00;color:#fff;' : '' }}">Tasks Only</a>
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
                                        <span style="font-weight:600;font-size:14px;color:#202124;">{{ $r->uploader?->full_name ?? 'Teacher' }}</span>
                                        <span style="font-size:13px;color:#5f6368;"> posted a new resource</span>
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
                                        @if($r->file_path)
                                            <a href="{{ route('student.resources.download', $r) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                                <span class="material-icons align-middle me-1" style="font-size:14px;">download</span>
                                                {{ $r->file_name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @php $t = $entry->item; @endphp
                @php
                    $streamSubmission = $submissions->get($t->id);
                    $streamStatus = \App\Models\Submission::statusFor($streamSubmission, $t);
                    $streamStatusColors = \App\Models\Submission::statusColors($streamStatus);
                    $streamGoogleFormUrl = null;
                    if (preg_match('/Google Form:\s*(https?:\/\/\S+)/i', (string) $t->description, $matches)) {
                        $streamGoogleFormUrl = $matches[1];
                    }
                @endphp
                <div class="card mb-3 stream-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#4a6cf7,#8fa8ff);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-icons" style="color:#fff;font-size:20px;">assignment</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span style="font-weight:600;font-size:14px;color:#202124;">{{ $t->creator?->full_name ?? 'Unknown User' }}</span>
                                        <span style="font-size:13px;color:#5f6368;"> posted a new assignment:
                                            <strong style="color:#202124;">{{ $t->title }}</strong>
                                        </span>
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
                                            Due: <strong style="color:{{ $streamStatusColors['text'] }};">
                                                {{ $t->due_date ? $t->due_date->format('M d, Y g:i A') : 'No due date' }}
                                            </strong>
                                        </span>
                                        <span style="font-size:12px;color:#5f6368;">
                                            <span class="material-icons align-middle me-1" style="font-size:14px;">grade</span>
                                            {{ $t->total_points }} points
                                        </span>
                                        @if($t->file_path)
                                            <a href="{{ route('student.tasks.download', $t) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                                <span class="material-icons align-middle me-1" style="font-size:14px;">attach_file</span>
                                                {{ $t->file_name }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('student.tasks.show', $t) }}" class="btn btn-sm rounded-pill px-3" style="background:#f9ab00;color:#fff;font-size:12px;">
                                            <span class="material-icons align-middle me-1" style="font-size:14px;">assignment</span>
                                            View Task
                                        </a>
                                        @if($streamGoogleFormUrl)
                                            <a href="{{ $streamGoogleFormUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill px-3 ms-1" style="font-size:12px;">
                                                <span class="material-icons align-middle me-1" style="font-size:14px;">open_in_new</span>
                                                Open Form
                                            </a>
                                        @endif
                                        <span class="badge rounded-pill ms-2" style="background:{{ $streamStatusColors['background'] }};color:{{ $streamStatusColors['text'] }};">
                                            {{ \App\Models\Submission::statusLabel($streamStatus) }}
                                        </span>
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
                    Resources and tasks will appear here once shared by your teacher.
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

@endsection

@push('scripts')
<script>
(() => {
    const btn = document.getElementById('copyStudentSubjectCodeBtn');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        const codeText = document.getElementById('studentSubjectCode')?.textContent?.replace('Code:', '').trim();
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
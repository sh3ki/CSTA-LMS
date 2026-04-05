@extends('layouts.student')
@section('title', $task->title)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">assignment</span>
            {{ $task->title }}
        </h1>
        <p class="page-subtitle">{{ $task->subject?->name }} @if($task->subject?->schoolClass) &middot; {{ $task->subject->schoolClass->name }} @endif</p>
    </div>
    <a href="{{ route('student.tasks.index') }}" class="btn btn-light rounded-pill px-3">
        <span class="material-icons align-middle me-1" style="font-size:16px;">arrow_back</span>
        Back to tasks
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <h4 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:6px;">Task Details</h4>
                        <div style="font-size:13px;color:#5f6368;">Assigned by {{ $task->subject?->schoolClass?->teacher?->full_name ?? 'Teacher' }}</div>
                    </div>
                    @if($task->file_path)
                        <a href="{{ route('student.tasks.download', $task) }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <span class="material-icons align-middle me-1" style="font-size:16px;">download</span>
                            Attachment
                        </a>
                    @endif
                </div>

                <div class="mb-4">
                    <div style="font-size:14px;line-height:1.7;color:#3c4043;white-space:pre-line;">{{ $task->description ?: 'No description provided.' }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="meta-box">
                            <div class="meta-label">Due Date</div>
                            <div class="meta-value" style="color:{{ $task->due_date->isPast() ? '#ea4335' : '#34a853' }};">{{ $task->due_date->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="meta-box">
                            <div class="meta-label">Points</div>
                            <div class="meta-value">{{ $task->total_points }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="meta-box">
                            <div class="meta-label">Status</div>
                            <div class="meta-value">
                                @if($submission)
                                    <span class="badge rounded-pill" style="background:#e6f4ea;color:#34a853;">Submitted</span>
                                @elseif($task->due_date->isPast())
                                    <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;">Past Due</span>
                                @else
                                    <span class="badge rounded-pill" style="background:#fef7e0;color:#f9ab00;">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="material-icons" style="color:#5f6368;font-size:18px;">upload_file</span>
                <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Submission</span>
            </div>
            <div class="card-body p-4">
                @if($submission)
                    <div class="mb-3">
                        <div class="info-row"><span>File</span><strong>{{ $submission->file_name }}</strong></div>
                        <div class="info-row"><span>Submitted</span><strong>{{ optional($submission->submitted_at)->format('M d, Y h:i A') }}</strong></div>
                        <div class="info-row"><span>Grade</span><strong>{{ $submission->grade !== null ? number_format($submission->grade, 2) : 'Pending review' }}</strong></div>
                    </div>
                    @if($submission->submission_note)
                        <div class="feedback-box mb-3">
                            <div class="feedback-label">Your Note</div>
                            <div style="font-size:14px;color:#3c4043;line-height:1.6;white-space:pre-line;">{{ $submission->submission_note }}</div>
                        </div>
                    @endif
                    @if($submission->feedback)
                        <div class="feedback-box">
                            <div class="feedback-label">Teacher Feedback</div>
                            <div style="font-size:14px;color:#3c4043;line-height:1.6;white-space:pre-line;">{{ $submission->feedback }}</div>
                        </div>
                    @endif
                @else
                    <p style="font-size:14px;color:#5f6368;margin-bottom:16px;">Upload your file to submit this task.</p>
                    <form action="{{ route('student.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="submission_note" class="form-control" rows="3" placeholder="Add context for your teacher...">{{ old('submission_note') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Submission File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <span class="material-icons align-middle me-1" style="font-size:16px;">upload</span>
                            Submit Task
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($submission && $submission->allow_resubmit)
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="material-icons" style="color:#5f6368;font-size:18px;">refresh</span>
                    <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Resubmit</span>
                </div>
                <div class="card-body p-4">
                    <p style="font-size:14px;color:#5f6368;margin-bottom:16px;">If your teacher allows it, you can upload a new file and replace your previous submission.</p>
                    <form action="{{ route('student.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Resubmission Note (optional)</label>
                            <textarea name="submission_note" class="form-control" rows="3" placeholder="Explain what you changed...">{{ old('submission_note') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Submission File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-outline-secondary rounded-pill px-4">
                            <span class="material-icons align-middle me-1" style="font-size:16px;">cloud_upload</span>
                            Resubmit Task
                        </button>
                    </form>
                </div>
            </div>
        @elseif($submission)
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2" style="color:#5f6368;font-size:14px;">
                        <span class="material-icons" style="font-size:18px;color:#f9ab00;">info</span>
                        Resubmission is disabled by your teacher for this task.
                    </div>
                </div>
            </div>
        @endif

        @if($submissionHistory->isNotEmpty())
            <div class="card mt-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="material-icons" style="color:#5f6368;font-size:18px;">history</span>
                    <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Submission History</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($submissionHistory as $attempt)
                            <div class="list-group-item" style="padding:12px 16px;">
                                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:#202124;">Attempt #{{ $attempt->attempt_number }}</div>
                                        <div style="font-size:12px;color:#5f6368;">{{ optional($attempt->submitted_at)->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div style="font-size:13px;color:#3c4043;">{{ $attempt->file_name }}</div>
                                </div>
                                @if($attempt->submission_note)
                                    <div style="margin-top:8px;font-size:13px;color:#5f6368;white-space:pre-line;">{{ $attempt->submission_note }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background:#f9ab00;border-color:#f9ab00; color:#fff; }
    .btn-primary:hover { background:#d98d00;border-color:#d98d00; }
    .meta-box { background:#f8f9fa;border:1px solid #e8eaed;border-radius:12px;padding:16px; }
    .meta-label { font-size:12px;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
    .meta-value { font-size:14px;font-weight:600;color:#202124; }
    .info-row { display:flex;justify-content:space-between;gap:12px;font-size:14px;padding:8px 0;border-bottom:1px solid #f1f3f4; }
    .info-row:last-child { border-bottom:none; }
    .info-row span { color:#5f6368; }
    .feedback-box { background:#f8f9fa;border:1px solid #e8eaed;border-radius:12px;padding:16px; }
    .feedback-label { font-size:12px;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
</style>
@endpush
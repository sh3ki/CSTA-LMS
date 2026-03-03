@extends('layouts.teacher')
@section('title', $task->title . ' — Submissions')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">assignment</span>
            {{ $task->title }}
        </h1>
        <p class="page-subtitle">
            {{ $task->subject->name }}
            @if($task->subject->schoolClass) &mdash; {{ $task->subject->schoolClass->name }} @endif
        </p>
    </div>
    <a href="{{ route('teacher.tasks.index') }}" class="btn btn-light rounded-pill px-3">
        <span class="material-icons align-middle me-1" style="font-size:16px;">arrow_back</span>
        Back to Tasks
    </a>
</div>

<!-- Task Info Card -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-8">
                <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:12px;">Task Details</h6>
                <p style="font-size:14px;color:#5f6368;line-height:1.7;">
                    {{ $task->description ?: 'No description provided.' }}
                </p>
                @if($task->file_name)
                    <div class="mt-3">
                        <a href="{{ route('teacher.tasks.download', $task) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <span class="material-icons align-middle me-1" style="font-size:14px;">attach_file</span>
                            {{ $task->file_name }}
                        </a>
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8f9fa;">
                            <div style="font-size:11px;color:#5f6368;text-transform:uppercase;letter-spacing:1px;">Due Date</div>
                            <div style="font-size:14px;font-weight:600;color:{{ $task->due_date->isPast() ? '#ea4335' : '#34a853' }};margin-top:4px;">
                                {{ $task->due_date->format('M d, Y') }}
                            </div>
                            <div style="font-size:12px;color:#5f6368;">{{ $task->due_date->format('h:i A') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8f9fa;">
                            <div style="font-size:11px;color:#5f6368;text-transform:uppercase;letter-spacing:1px;">Points</div>
                            <div style="font-size:14px;font-weight:600;color:#800020;margin-top:4px;">{{ $task->total_points }}</div>
                            <div style="font-size:12px;color:#5f6368;">Max score</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8f9fa;">
                            <div style="font-size:11px;color:#5f6368;text-transform:uppercase;letter-spacing:1px;">Submitted</div>
                            <div style="font-size:14px;font-weight:600;color:#34a853;margin-top:4px;">{{ $submissions->count() }}</div>
                            <div style="font-size:12px;color:#5f6368;">of {{ $students->count() }} students</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f8f9fa;">
                            <div style="font-size:11px;color:#5f6368;text-transform:uppercase;letter-spacing:1px;">Graded</div>
                            @php $graded = $submissions->filter(fn($s) => $s->grade !== null)->count(); @endphp
                            <div style="font-size:14px;font-weight:600;color:#f9ab00;margin-top:4px;">{{ $graded }}</div>
                            <div style="font-size:12px;color:#5f6368;">of {{ $submissions->count() }} submissions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table -->
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="material-icons" style="color:#5f6368;font-size:18px;">grading</span>
        <span style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;font-size:15px;color:#202124;">Student Submissions</span>
        <span class="badge rounded-pill ms-1" style="background:#e6f4ea;color:#34a853;font-size:12px;">{{ $students->count() }} students</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>ID Number</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Submitted At</th>
                    <th>Grade</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php $submission = $submissions->get($student->id); @endphp
                    <tr>
                        <td style="color:#5f6368;width:48px;">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:600;">
                                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                </div>
                                <span style="font-weight:500;">{{ $student->full_name }}</span>
                            </div>
                        </td>
                        <td><code style="background:#f1f3f4;padding:3px 8px;border-radius:6px;font-size:13px;">{{ $student->id_number }}</code></td>
                        <td>
                            @if($submission)
                                <span class="badge rounded-pill" style="background:#e6f4ea;color:#34a853;font-size:12px;padding:4px 12px;">Submitted</span>
                            @else
                                <span class="badge rounded-pill" style="background:#fce8e6;color:#ea4335;font-size:12px;padding:4px 12px;">Not Submitted</span>
                            @endif
                        </td>
                        <td>
                            @if($submission && $submission->file_path)
                                <a href="{{ route('teacher.submissions.download', $submission) }}" style="font-size:13px;color:#800020;text-decoration:none;">
                                    <span class="material-icons align-middle" style="font-size:14px;">download</span>
                                    {{ $submission->file_name }}
                                </a>
                            @else
                                <span style="color:#dadce0;font-size:13px;">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:#5f6368;">
                            {{ $submission && $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : '—' }}
                        </td>
                        <td>
                            @if($submission && $submission->grade !== null)
                                @php
                                    $pct = ($submission->grade / $task->total_points) * 100;
                                    $gradeColor = $pct >= 75 ? '#34a853' : ($pct >= 50 ? '#f9ab00' : '#ea4335');
                                @endphp
                                <span style="font-weight:600;color:{{ $gradeColor }};">{{ $submission->grade }}</span>
                                <span style="color:#5f6368;font-size:12px;">/{{ $task->total_points }}</span>
                            @elseif($submission)
                                <span style="color:#f9ab00;font-size:13px;font-weight:500;">Pending</span>
                            @else
                                <span style="color:#dadce0;font-size:13px;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @if($submission)
                                    <button class="btn-icon" title="Grade"
                                        data-bs-toggle="modal" data-bs-target="#gradeModal"
                                        data-submission_id="{{ $submission->id }}"
                                        data-student_name="{{ $student->full_name }}"
                                        data-grade="{{ $submission->grade }}"
                                        data-feedback="{{ $submission->feedback }}"
                                        data-total_points="{{ $task->total_points }}">
                                        <span class="material-icons" style="color:#800020;">grading</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <span class="material-icons d-block mb-2" style="font-size:48px;color:#dadce0;">groups</span>
                            <div style="color:#5f6368;font-size:15px;">No students enrolled in this class.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ══ GRADE MODAL ══ -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#fce8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#800020;font-size:20px;">grading</span>
                    </div>
                    <h5 class="modal-title">Grade Submission</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="gradeForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <p id="gradeStudentName" style="font-size:14px;color:#5f6368;margin-bottom:20px;"></p>
                    <div class="mb-3">
                        <label class="form-label">Grade <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="grade" id="grade_value" class="form-control" step="0.01" min="0" required>
                            <span class="input-group-text" id="grade_max">/100</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Feedback <span class="text-muted">(optional)</span></label>
                        <textarea name="feedback" id="grade_feedback" class="form-control" rows="3"
                            placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Save Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table th { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#5f6368;border-bottom:2px solid #e8eaed;padding:12px 16px;white-space:nowrap; }
    .table td { padding:12px 16px;vertical-align:middle;border-bottom:1px solid #f1f3f4;font-size:14px;color:#202124; }
    .table tbody tr:hover { background:#f8f9fa; }
    .btn-icon { background:none;border:none;width:36px;height:36px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s; }
    .btn-icon:hover { background:#f1f3f4; }
    .btn-primary { background:#800020;border-color:#800020; }
    .btn-primary:hover { background:#5c0016;border-color:#5c0016; }
</style>
@endpush

@push('scripts')
<script>
    // ── Grade Modal
    document.getElementById('gradeModal').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        document.getElementById('gradeForm').action = `/teacher/submissions/${btn.dataset.submission_id}/grade`;
        document.getElementById('gradeStudentName').textContent = `Grading submission for: ${btn.dataset.student_name}`;
        document.getElementById('grade_value').value   = btn.dataset.grade || '';
        document.getElementById('grade_value').max     = btn.dataset.total_points;
        document.getElementById('grade_max').textContent = `/${btn.dataset.total_points}`;
        document.getElementById('grade_feedback').value = btn.dataset.feedback || '';
    });
</script>
@endpush

@extends('layouts.teacher')
@section('title', 'Student Analytics — ' . $student->full_name)

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('teacher.analytics.index') }}" class="btn btn-light rounded-circle p-2" title="Back to Analytics">
            <span class="material-icons" style="font-size:20px;">arrow_back</span>
        </a>
        <div>
            <h1 class="page-title" style="margin-bottom:2px;">
                <span class="material-icons align-middle me-2">person</span>
                {{ $student->full_name }}
            </h1>
            <p class="page-subtitle" style="margin:0;">{{ $student->id_number }} &middot; Individual Performance Analytics</p>
        </div>
    </div>
    <a href="{{ route('teacher.analytics.index') }}?view=student" class="btn btn-outline-secondary rounded-pill px-4">
        <span class="material-icons align-middle me-1" style="font-size:16px;">group</span>
        All Students
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;"><span class="material-icons" style="color:#0d652d;">check_circle</span></div>
            <div><div class="stat-value">{{ $submissions->count() }}</div><div class="stat-label">Submitted</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;"><span class="material-icons" style="color:#1a73e8;">grade</span></div>
            <div><div class="stat-value">{{ $graded->count() }}</div><div class="stat-label">Graded Tasks</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;"><span class="material-icons" style="color:#f9ab00;">star</span></div>
            <div><div class="stat-value">{{ $avgGrade ?? '—' }}</div><div class="stat-label">Average Grade</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8e6;"><span class="material-icons" style="color:#c5221f;">assignment_late</span></div>
            <div><div class="stat-value">{{ $late }}</div><div class="stat-label">Late Submissions</div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Grade Distribution</span></div>
            <div class="card-body"><canvas id="gradeBar" style="max-height:220px;"></canvas></div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Monthly Submissions (Last 6 Months)</span></div>
            <div class="card-body"><canvas id="monthlyLine" style="max-height:220px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Submission Timeliness</span></div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="timePie" style="max-height:200px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Performance by Subject</span></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead><tr><th>Subject</th><th>Tasks</th><th>Avg Grade</th><th>Highest</th><th>Lowest</th></tr></thead>
                    <tbody>
                        @forelse($subjectPerformance as $sp)
                        <tr>
                            <td style="font-weight:500;">{{ $sp['name'] }}</td>
                            <td>{{ $sp['count'] }}</td>
                            <td>
                                @php $avg = is_numeric($sp['avg_grade']) ? $sp['avg_grade'] : null; @endphp
                                <span class="badge" style="background:{{ $avg !== null && $avg >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $avg !== null && $avg >= 75 ? '#0d652d' : '#c5221f' }};">
                                    {{ $sp['avg_grade'] }}
                                </span>
                            </td>
                            <td>{{ $sp['highest'] }}</td>
                            <td>{{ $sp['lowest'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No graded tasks yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- All Submissions -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;">All Submissions</span>
        <span style="font-size:13px;color:#5f6368;">{{ $submissions->count() }} total</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead><tr><th>Task</th><th>Subject</th><th>Submitted</th><th>Grade</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td style="font-weight:500;">{{ $sub->task->title ?? '—' }}</td>
                    <td>{{ $sub->task->subject->name ?? '—' }}</td>
                    <td>{{ $sub->submitted_at?->format('M d, Y') }}</td>
                    <td>
                        @if($sub->grade !== null)
                            <span class="badge" style="background:{{ $sub->grade >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $sub->grade >= 75 ? '#0d652d' : '#c5221f' }};">
                                {{ $sub->grade }} / {{ $sub->task->total_points ?? '—' }}
                            </span>
                        @else
                            <span class="badge" style="background:#fef7e0;color:#9c5900;">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($sub->task && $sub->task->due_date && $sub->submitted_at > $sub->task->due_date)
                            <span class="badge" style="background:#fce8e6;color:#c5221f;">Late</span>
                        @else
                            <span class="badge" style="background:#e6f4ea;color:#0d652d;">On Time</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
.stat-card {
    background: #fff; border-radius: 16px; border: 1px solid #e8eaed;
    padding: 20px 24px; display: flex; align-items: center; gap: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: box-shadow .2s, transform .15s;
}
.stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-2px); }
.stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon .material-icons { font-size: 26px; }
.stat-value { font-family: 'Google Sans', Roboto, sans-serif; font-size: 28px; font-weight: 700; color: #202124; line-height: 1; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: #5f6368; }
.card { border: 1px solid #e8eaed; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.card-header { background: #fff; border-bottom: 1px solid #f1f3f4; padding: 14px 20px; border-radius: 12px 12px 0 0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('gradeBar'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($gradeRanges)) !!},
        datasets: [{ label: 'Tasks', data: {!! json_encode(array_values($gradeRanges)) !!}, backgroundColor: ['#34a853','#1a73e8','#f9ab00','#ea8600','#ea4335'] }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('monthlyLine'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyLabels) !!},
        datasets: [{ label: 'Submissions', data: {!! json_encode($monthlyCounts) !!}, borderColor: '#800020', backgroundColor: 'rgba(128,0,32,0.1)', tension: 0.4, fill: true }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('timePie'), {
    type: 'doughnut',
    data: {
        labels: ['On Time', 'Late'],
        datasets: [{ data: [{{ $onTime }}, {{ $late }}], backgroundColor: ['#34a853','#ea4335'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush

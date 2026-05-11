@extends('layouts.admin')
@section('title', 'Teacher Analytics — ' . $teacher->full_name)

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.analytics.index') }}" class="btn btn-light rounded-circle p-2" title="Back to Analytics">
            <span class="material-icons" style="font-size:20px;">arrow_back</span>
        </a>
        <div>
            <h1 class="page-title" style="margin-bottom:2px;">
                <span class="material-icons align-middle me-2" style="color:#800020;">supervisor_account</span>
                {{ $teacher->full_name }}
            </h1>
            <p class="page-subtitle" style="margin:0;">{{ $teacher->id_number }} &middot; Individual Teacher Analytics</p>
        </div>
    </div>
    <a href="{{ route('admin.analytics.index') }}#teacher" class="btn btn-outline-secondary rounded-pill px-4">
        <span class="material-icons align-middle me-1" style="font-size:16px;">group</span> All Teachers
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2" style="min-width:160px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;"><span class="material-icons" style="color:#0d652d;">class</span></div>
            <div><div class="stat-value">{{ $totalClasses }}</div><div class="stat-label">Classes</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2" style="min-width:160px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;"><span class="material-icons" style="color:#1a73e8;">school</span></div>
            <div><div class="stat-value">{{ $totalStudents }}</div><div class="stat-label">Students</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2" style="min-width:160px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8fd;"><span class="material-icons" style="color:#9334e6;">assignment</span></div>
            <div><div class="stat-value">{{ $totalTasks }}</div><div class="stat-label">Tasks Created</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2" style="min-width:160px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce8ec;"><span class="material-icons" style="color:#800020;">folder</span></div>
            <div><div class="stat-value">{{ $resourcesCount }}</div><div class="stat-label">Resources Uploaded</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2" style="min-width:160px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;"><span class="material-icons" style="color:#f9ab00;">star</span></div>
            <div><div class="stat-value">{{ $avgGrade ? round($avgGrade, 1) : '—' }}</div><div class="stat-label">Avg Student Grade</div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Students' Grade Distribution</span></div>
            <div class="card-body"><canvas id="gradeBar" style="max-height:220px;"></canvas></div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Tasks Created per Month (Last 6 Months)</span></div>
            <div class="card-body"><canvas id="monthlyLine" style="max-height:220px;"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Per-Class Breakdown</span></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead><tr><th>Class</th><th>Students</th><th>Tasks</th><th>Avg Grade</th></tr></thead>
                    <tbody>
                        @forelse($classBreakdown as $cb)
                        <tr>
                            <td style="font-weight:500;">{{ $cb['name'] }}</td>
                            <td>{{ $cb['students'] }}</td>
                            <td>{{ $cb['tasks'] }}</td>
                            <td>
                                @if($cb['avg_grade'] > 0)
                                    <span class="badge" style="background:{{ $cb['avg_grade'] >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $cb['avg_grade'] >= 75 ? '#0d652d' : '#c5221f' }};">{{ $cb['avg_grade'] }}</span>
                                @else
                                    <span class="badge" style="background:#f1f3f4;color:#9aa0a6;">No data</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No classes yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Subject Performance</span></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead><tr><th>Subject</th><th>Tasks</th><th>Submissions</th><th>Avg Grade</th></tr></thead>
                    <tbody>
                        @forelse($subjectPerformance as $sp)
                        <tr>
                            <td style="font-weight:500;">{{ $sp['name'] }}</td>
                            <td>{{ $sp['tasks'] }}</td>
                            <td>{{ $sp['submissions'] }}</td>
                            <td>
                                @if($sp['avg_grade'] > 0)
                                    <span class="badge" style="background:{{ $sp['avg_grade'] >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $sp['avg_grade'] >= 75 ? '#0d652d' : '#c5221f' }};">{{ $sp['avg_grade'] }}</span>
                                @else
                                    <span class="badge" style="background:#f1f3f4;color:#9aa0a6;">No data</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No subjects yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('gradeBar'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($gradeRanges)) !!},
        datasets: [{ label: 'Students', data: {!! json_encode(array_values($gradeRanges)) !!}, backgroundColor: ['#34a853','#1a73e8','#f9ab00','#ea8600','#ea4335'] }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('monthlyLine'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyLabels) !!},
        datasets: [{ label: 'Tasks Created', data: {!! json_encode($monthlyCounts) !!}, borderColor: '#800020', backgroundColor: 'rgba(128,0,32,0.1)', tension: 0.4, fill: true }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
@endpush

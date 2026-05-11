@extends('layouts.admin')
@section('title', 'Reports')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">bar_chart</span>
            Reports &amp; Analytics
        </h1>
        <p class="page-subtitle">View system-wide reports and performance analytics.</p>
    </div>
    <a href="{{ route('admin.reports.export') }}" class="btn btn-outline-secondary rounded-pill px-4 d-flex align-items-center gap-2">
        <span class="material-icons" style="font-size:18px;">download</span>
        Export CSV
    </a>
</div>

@include('partials._toasts')

<!-- Stats -->
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Teachers','value'=>$stats['teachers'],'icon'=>'person_outline','bg'=>'#fce8ec','color'=>'#800020'],
        ['label'=>'Students','value'=>$stats['students'],'icon'=>'school','bg'=>'#e6f4ea','color'=>'#34a853'],
        ['label'=>'Classes','value'=>$stats['classes'],'icon'=>'class','bg'=>'#fce8e6','color'=>'#ea4335'],
        ['label'=>'Subjects','value'=>$stats['subjects'],'icon'=>'menu_book','bg'=>'#fef7e0','color'=>'#f9ab00'],
        ['label'=>'Resources','value'=>$stats['resources'],'icon'=>'folder_open','bg'=>'#e8f0fe','color'=>'#1a73e8'],
        ['label'=>'Tasks','value'=>$stats['tasks'],'icon'=>'assignment','bg'=>'#f3e8fd','color'=>'#9334e6'],
        ['label'=>'Submissions','value'=>$stats['total_submissions'],'icon'=>'check_circle','bg'=>'#e6f4ea','color'=>'#0d652d'],
        ['label'=>'Pending Grades','value'=>$stats['pending_grades'],'icon'=>'pending','bg'=>'#fff3e0','color'=>'#e65100'],
    ] as $s)
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:{{ $s['bg'] }};">
                <span class="material-icons" style="color:{{ $s['color'] }};">{{ $s['icon'] }}</span>
            </div>
            <div>
                <div class="stat-value">{{ $s['value'] }}</div>
                <div class="stat-label">{{ $s['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <!-- Submission Breakdown -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Submission Status</span></div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="submissionPie" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Grade Distribution -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><span style="font-weight:600;">Grade Distribution</span></div>
            <div class="card-body">
                <canvas id="gradeBar" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Top Classes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><span style="font-weight:600;">Classes by Student Count</span></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead><tr><th>Class</th><th class="text-end">Students</th></tr></thead>
                    <tbody>
                        @foreach($topClasses as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td class="text-end"><span class="badge" style="background:#e6f4ea;color:#0d652d;">{{ $c->students_count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Top Subjects -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><span style="font-weight:600;">Subjects by Task Count</span></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:13px;">
                    <thead><tr><th>Subject</th><th class="text-end">Tasks</th></tr></thead>
                    <tbody>
                        @foreach($topSubjects as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td class="text-end"><span class="badge" style="background:#e8f0fe;color:#1a73e8;">{{ $s->tasks_count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Submissions Chart -->
<div class="card mb-4">
    <div class="card-header"><span style="font-weight:600;">Monthly Submissions (Last 6 Months)</span></div>
    <div class="card-body">
        <canvas id="monthlyLine" style="max-height:250px;"></canvas>
    </div>
</div>

<!-- Recent Submissions -->
<div class="card">
    <div class="card-header"><span style="font-weight:600;">Recent Submissions</span></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead><tr><th>Student</th><th>Task</th><th>Subject</th><th>Submitted</th><th>Grade</th></tr></thead>
            <tbody>
                @foreach($recentSubmissions as $sub)
                <tr>
                    <td>{{ $sub->student->full_name ?? '—' }}</td>
                    <td>{{ $sub->task->title ?? '—' }}</td>
                    <td>{{ $sub->task->subject->name ?? '—' }}</td>
                    <td>{{ $sub->submitted_at->format('M d, Y H:i') }}</td>
                    <td>
                        @if($sub->grade !== null)
                            {{ $sub->grade }}
                        @else
                            <span class="badge" style="background:#fef7e0;color:#9c5900;">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('submissionPie'), {
    type: 'doughnut',
    data: {
        labels: ['On Time', 'Late'],
        datasets: [{
            data: [{{ $submissionBreakdown['on_time'] }}, {{ $submissionBreakdown['late'] }}],
            backgroundColor: ['#34a853','#ea4335'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('gradeBar'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($gradeRanges)) !!},
        datasets: [{
            label: 'Students',
            data: {!! json_encode(array_values($gradeRanges)) !!},
            backgroundColor: ['#34a853','#1a73e8','#f9ab00','#ea8600','#ea4335'],
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('monthlyLine'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlySubmissions, 'label')) !!},
        datasets: [{
            label: 'Submissions',
            data: {!! json_encode(array_column($monthlySubmissions, 'count')) !!},
            borderColor: '#800020',
            backgroundColor: 'rgba(128,0,32,0.1)',
            tension: 0.4,
            fill: true,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush

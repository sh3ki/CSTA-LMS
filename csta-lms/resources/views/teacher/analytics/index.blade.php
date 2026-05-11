@extends('layouts.teacher')
@section('title', 'Analytics')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2">insights</span>
            Performance Analytics
        </h1>
        <p class="page-subtitle">Performance insights across your classes and subjects.</p>
    </div>
</div>

<!-- Class Filter -->
<div class="d-flex align-items-center gap-3 mb-3" style="background:#f8f9fa;border-radius:10px;padding:10px 16px;border:1px solid #e8eaed;">
    <span class="material-icons" style="font-size:18px;color:#800020;">filter_list</span>
    <span style="font-size:13px;color:#5f6368;font-weight:500;">Showing data for:</span>
    <form method="GET" class="d-flex align-items-center gap-2 mb-0">
        <select name="class_id" class="form-select form-select-sm" style="width:auto;min-width:210px;" onchange="this.form.submit()">
            <option value="">All My Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @if($selectedClassId)
            <a href="{{ route('teacher.analytics.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <span class="material-icons" style="font-size:14px;vertical-align:middle;">close</span> Clear
            </a>
        @endif
    </form>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;"><span class="material-icons" style="color:#0d652d;">school</span></div>
            <div><div class="stat-value">{{ $totalStudents }}</div><div class="stat-label">My Students</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8fd;"><span class="material-icons" style="color:#9334e6;">assignment</span></div>
            <div><div class="stat-value">{{ $totalTasks }}</div><div class="stat-label">Tasks Created</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;"><span class="material-icons" style="color:#1a73e8;">check_circle</span></div>
            <div><div class="stat-value">{{ $totalSubmissions }}</div><div class="stat-label">Submissions</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;"><span class="material-icons" style="color:#f9ab00;">grade</span></div>
            <div><div class="stat-value">{{ $avgGrade ? round($avgGrade, 1) : '—' }}</div><div class="stat-label">Average Grade</div></div>
        </div>
    </div>
</div>

<!-- View Toggle -->
<div class="d-flex align-items-center gap-2 mb-4">
    <div class="analytics-toggle">
        <button class="toggle-btn active" id="btnClassView" onclick="switchView('class')">
            <span class="material-icons" style="font-size:16px;">bar_chart</span>
            Class Analytics
        </button>
        <button class="toggle-btn" id="btnStudentView" onclick="switchView('student')">
            <span class="material-icons" style="font-size:16px;">person</span>
            Individual Student
        </button>
    </div>
</div>

<!-- ── CLASS ANALYTICS VIEW ── -->
<div id="classView">
    <div class="row g-4 mb-4">
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header"><span style="font-weight:600;">Grade Distribution</span></div>
                <div class="card-body"><canvas id="gradeBar" style="max-height:230px;"></canvas></div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header"><span style="font-weight:600;">Monthly Submissions (Last 6 Months)</span></div>
                <div class="card-body"><canvas id="monthlyLine" style="max-height:230px;"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><span style="font-weight:600;">Class Performance</span></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead><tr><th>Class</th><th>Students</th><th>Avg Grade</th><th>Submissions</th></tr></thead>
                        <tbody>
                            @forelse($classPerformance as $cp)
                            <tr>
                                <td>{{ $cp['name'] }}</td>
                                <td>{{ $cp['students'] }}</td>
                                <td><span class="badge" style="background:{{ $cp['avg_grade'] >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $cp['avg_grade'] >= 75 ? '#0d652d' : '#c5221f' }};">{{ $cp['avg_grade'] }}</span></td>
                                <td>{{ $cp['submissions'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><span style="font-weight:600;">Subject Performance</span></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead><tr><th>Subject</th><th>Tasks</th><th>Avg Grade</th></tr></thead>
                        <tbody>
                            @forelse($subjectPerformance as $sp)
                            <tr>
                                <td>{{ $sp['name'] }}</td>
                                <td>{{ $sp['tasks'] }}</td>
                                <td><span class="badge" style="background:{{ $sp['avg_grade'] >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $sp['avg_grade'] >= 75 ? '#0d652d' : '#c5221f' }};">{{ $sp['avg_grade'] }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-weight:600;">Top Students</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead><tr><th>#</th><th>Student</th><th>ID Number</th><th>Avg Grade</th><th>Graded Tasks</th><th></th></tr></thead>
                <tbody>
                    @forelse($topStudents as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight:500;">{{ $s['name'] }}</td>
                        <td style="color:#9aa0a6;">{{ $s['id_number'] }}</td>
                        <td><span class="badge" style="background:#e6f4ea;color:#0d652d;">{{ $s['avg_grade'] }}</span></td>
                        <td>{{ $s['submitted'] }}</td>
                        <td>
                            <a href="{{ route('teacher.analytics.student', $s['id']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                <span class="material-icons" style="font-size:13px;vertical-align:middle;">person</span>
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── INDIVIDUAL STUDENT VIEW ── -->
<div id="studentView" style="display:none;">
    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <label style="font-size:14px;font-weight:600;white-space:nowrap;margin:0;">Select Class:</label>
                <select id="classFilter" class="form-select" style="width:auto;min-width:200px;font-size:14px;" onchange="filterStudents()">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <label style="font-size:14px;font-weight:600;white-space:nowrap;margin:0;">Select Student:</label>
                <select id="studentSelect" class="form-select" style="width:auto;min-width:220px;font-size:14px;">
                    <option value="">— choose a student —</option>
                    @foreach($classes as $class)
                        @foreach($class->students as $student)
                            <option value="{{ route('teacher.analytics.student', $student->id) }}" data-class="{{ $class->id }}">
                                {{ $student->full_name }} ({{ $student->id_number }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <button onclick="goToStudent()" class="btn btn-primary rounded-pill px-4" style="font-size:14px;">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">bar_chart</span>
                    View Analytics
                </button>
            </div>
        </div>
    </div>

    <div class="card p-5 text-center" style="border:2px dashed #e8eaed;">
        <span class="material-icons d-block mb-3" style="font-size:56px;color:#dadce0;">person_search</span>
        <div style="font-size:15px;font-weight:600;color:#3c4043;">Select a student above to view their individual analytics</div>
        <div style="font-size:13px;color:#5f6368;margin-top:6px;">Grades, submission history, timeliness, and subject performance</div>
    </div>
</div>

@endsection

@push('styles')
<style>
.stat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8eaed;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    transition: box-shadow .2s, transform .15s;
}
.stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-2px); }
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon .material-icons { font-size: 26px; }
.stat-value {
    font-family: 'Google Sans', Roboto, sans-serif;
    font-size: 28px; font-weight: 700; color: #202124;
    line-height: 1; margin-bottom: 4px;
}
.stat-label { font-size: 13px; color: #5f6368; }
.card { border: 1px solid #e8eaed; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.card-header { background: #fff; border-bottom: 1px solid #f1f3f4; padding: 14px 20px; border-radius: 12px 12px 0 0; }
.analytics-toggle {
    display: inline-flex;
    background: #f1f3f4;
    border-radius: 24px;
    padding: 4px;
    gap: 2px;
}
.toggle-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    border: none; background: transparent;
    border-radius: 20px;
    font-size: 14px; font-weight: 500; color: #5f6368;
    cursor: pointer; transition: all .2s;
}
.toggle-btn.active {
    background: #fff;
    color: #800020;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
}
.toggle-btn .material-icons { color: inherit; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function switchView(view) {
    document.getElementById('classView').style.display   = view === 'class'   ? '' : 'none';
    document.getElementById('studentView').style.display = view === 'student' ? '' : 'none';
    document.getElementById('btnClassView').classList.toggle('active',   view === 'class');
    document.getElementById('btnStudentView').classList.toggle('active', view === 'student');
}

function filterStudents() {
    const classId = document.getElementById('classFilter').value;
    const sel = document.getElementById('studentSelect');
    sel.querySelectorAll('option').forEach(opt => {
        if (!opt.value) return;
        opt.hidden = classId ? opt.dataset.class !== classId : false;
    });
    sel.value = '';
}

function goToStudent() {
    const url = document.getElementById('studentSelect').value;
    if (url) window.location.href = url;
}

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
        datasets: [{ label: 'Submissions', data: {!! json_encode($monthlyCounts) !!}, borderColor: '#800020', backgroundColor: 'rgba(128,0,32,0.1)', tension: 0.4, fill: true }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
@endpush

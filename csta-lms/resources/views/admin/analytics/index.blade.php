@extends('layouts.admin')
@section('title', 'Performance Analytics')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">insights</span>
            Performance Analytics
        </h1>
        <p class="page-subtitle">System-wide performance metrics, class insights, and individual analytics.</p>
    </div>
</div>

@include('partials._toasts')

<!-- Overview Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;"><span class="material-icons" style="color:#0d652d;">school</span></div>
            <div><div class="stat-value">{{ $totalStudents }}</div><div class="stat-label">Total Students</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8fd;"><span class="material-icons" style="color:#9334e6;">assignment</span></div>
            <div><div class="stat-value">{{ $totalTasks }}</div><div class="stat-label">Total Tasks</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;"><span class="material-icons" style="color:#1a73e8;">check_circle</span></div>
            <div><div class="stat-value">{{ $submissionRate }}%</div><div class="stat-label">Submission Rate</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef7e0;"><span class="material-icons" style="color:#f9ab00;">grade</span></div>
            <div><div class="stat-value">{{ $avgGrade ? round($avgGrade, 1) : '—' }}</div><div class="stat-label">Avg Grade</div></div>
        </div>
    </div>
</div>

<!-- View Toggle -->
<div class="d-flex align-items-center gap-2 mb-4">
    <div class="analytics-toggle">
        <button class="toggle-btn active" id="btnClassView" onclick="switchView('class')">
            <span class="material-icons" style="font-size:16px;">bar_chart</span> Class Analytics
        </button>
        <button class="toggle-btn" id="btnStudentView" onclick="switchView('student')">
            <span class="material-icons" style="font-size:16px;">person</span> Individual Student
        </button>
        <button class="toggle-btn" id="btnTeacherView" onclick="switchView('teacher')">
            <span class="material-icons" style="font-size:16px;">supervisor_account</span> Individual Teacher
        </button>
    </div>
</div>

<!-- ─── CLASS ANALYTICS ─── -->
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
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><span style="font-weight:600;">On-Time vs Late</span></div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="timePie" style="max-height:200px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header"><span style="font-weight:600;">Class Performance</span></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead><tr><th>Class</th><th>Teacher</th><th>Students</th><th>Avg Grade</th><th>Submissions</th></tr></thead>
                        <tbody>
                            @forelse($classPerformance as $cp)
                            <tr>
                                <td style="font-weight:500;">{{ $cp['name'] }}</td>
                                <td style="color:#5f6368;">{{ $cp['teacher'] }}</td>
                                <td>{{ $cp['students'] }}</td>
                                <td><span class="badge" style="background:{{ $cp['avg_grade'] >= 75 ? '#e6f4ea' : '#fce8e6' }};color:{{ $cp['avg_grade'] >= 75 ? '#0d652d' : '#c5221f' }};">{{ $cp['avg_grade'] }}</span></td>
                                <td>{{ $cp['submissions'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><span style="font-weight:600;">Top Students</span></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead><tr><th>#</th><th>Student</th><th>Avg Grade</th><th>Graded Tasks</th><th></th></tr></thead>
                        <tbody>
                            @forelse($topStudents as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div style="font-weight:500;">{{ $s['name'] }}</div>
                                    <div style="font-size:11px;color:#9aa0a6;">{{ $s['id_number'] }}</div>
                                </td>
                                <td><span class="badge" style="background:#e6f4ea;color:#0d652d;">{{ $s['avg_grade'] }}</span></td>
                                <td>{{ $s['submitted'] }}</td>
                                <td>
                                    <a href="{{ route('admin.analytics.student', $s['id']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                        <span class="material-icons" style="font-size:13px;vertical-align:middle;">person</span> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><span style="font-weight:600;">Teacher Activity</span></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size:13px;">
                        <thead><tr><th>Teacher</th><th>Classes</th><th>Students</th><th>Tasks</th><th>Resources</th><th></th></tr></thead>
                        <tbody>
                            @forelse($teacherActivity as $t)
                            <tr>
                                <td style="font-weight:500;">{{ $t['name'] }}</td>
                                <td>{{ $t['classes'] }}</td>
                                <td>{{ $t['students'] }}</td>
                                <td>{{ $t['tasks'] }}</td>
                                <td>{{ $t['resources'] }}</td>
                                <td>
                                    <a href="{{ route('admin.analytics.teacher', $t['id']) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:12px;">
                                        <span class="material-icons" style="font-size:13px;vertical-align:middle;">supervisor_account</span> View
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
    </div>
</div>

<!-- ─── INDIVIDUAL STUDENT ─── -->
<div id="studentView" style="display:none;">
    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <label style="font-size:14px;font-weight:600;white-space:nowrap;margin:0;">Filter by Class:</label>
                <select id="classFilterStudent" class="form-select" style="width:auto;min-width:210px;font-size:14px;" onchange="filterStudents()">
                    <option value="">All Classes</option>
                    @foreach($allClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <label style="font-size:14px;font-weight:600;white-space:nowrap;margin:0;">Select Student:</label>
                <select id="studentSelect" class="form-select" style="width:auto;min-width:230px;font-size:14px;">
                    <option value="">— choose a student —</option>
                    @foreach($allClasses as $class)
                        @foreach($class->students as $student)
                            <option value="{{ route('admin.analytics.student', $student->id) }}" data-class="{{ $class->id }}">
                                {{ $student->full_name }} ({{ $student->id_number }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <button onclick="goToStudent()" class="btn btn-primary rounded-pill px-4" style="font-size:14px;background:#800020;border-color:#800020;">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">bar_chart</span> View Analytics
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

<!-- ─── INDIVIDUAL TEACHER ─── -->
<div id="teacherView" style="display:none;">
    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <label style="font-size:14px;font-weight:600;white-space:nowrap;margin:0;">Select Teacher:</label>
                <select id="teacherSelect" class="form-select" style="width:auto;min-width:260px;font-size:14px;">
                    <option value="">— choose a teacher —</option>
                    @foreach($allTeachers as $teacher)
                        <option value="{{ route('admin.analytics.teacher', $teacher->id) }}">
                            {{ $teacher->full_name }} ({{ $teacher->id_number }})
                        </option>
                    @endforeach
                </select>
                <button onclick="goToTeacher()" class="btn btn-primary rounded-pill px-4" style="font-size:14px;background:#800020;border-color:#800020;">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">bar_chart</span> View Analytics
                </button>
            </div>
        </div>
    </div>
    <div class="card p-5 text-center" style="border:2px dashed #e8eaed;">
        <span class="material-icons d-block mb-3" style="font-size:56px;color:#dadce0;">supervisor_account</span>
        <div style="font-size:15px;font-weight:600;color:#3c4043;">Select a teacher above to view their analytics</div>
        <div style="font-size:13px;color:#5f6368;margin-top:6px;">Classes taught, tasks created, resources uploaded, and student performance</div>
    </div>
</div>

@endsection

@push('styles')
<style>
.analytics-toggle {
    display: inline-flex; background: #f1f3f4; border-radius: 24px; padding: 4px; gap: 2px;
}
.toggle-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
    border: none; background: transparent; border-radius: 20px;
    font-size: 14px; font-weight: 500; color: #5f6368; cursor: pointer; transition: all .2s;
}
.toggle-btn.active { background: #fff; color: #800020; box-shadow: 0 1px 4px rgba(0,0,0,.12); }
.toggle-btn .material-icons { color: inherit; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function switchView(view) {
    ['class','student','teacher'].forEach(function(v) {
        document.getElementById(v + 'View').style.display = v === view ? '' : 'none';
        var btn = document.getElementById('btn' + v.charAt(0).toUpperCase() + v.slice(1) + 'View');
        btn.classList.toggle('active', v === view);
    });
}
function filterStudents() {
    var classId = document.getElementById('classFilterStudent').value;
    document.getElementById('studentSelect').querySelectorAll('option').forEach(function(opt) {
        if (!opt.value) return;
        opt.hidden = classId ? opt.dataset.class !== classId : false;
    });
    document.getElementById('studentSelect').value = '';
}
function goToStudent() {
    var url = document.getElementById('studentSelect').value;
    if (url) window.location.href = url;
}
function goToTeacher() {
    var url = document.getElementById('teacherSelect').value;
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

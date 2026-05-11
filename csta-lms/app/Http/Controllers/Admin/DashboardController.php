<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers'    => User::where('role', 'teacher')->count(),
            'students'    => User::where('role', 'student')->count(),
            'classes'     => SchoolClass::count(),
            'subjects'    => Subject::count(),
            'resources'   => Resource::count(),
            'tasks'       => Task::count(),
            'submissions' => Submission::whereNotNull('submitted_at')->count(),
            'pending'     => Submission::whereNotNull('submitted_at')->whereNull('grade')->count(),
        ];

        // Recent activity (latest audit logs)
        $recentActivity = AuditLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        // Recent registrations
        $recentUsers = User::whereIn('role', ['teacher', 'student'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivity', 'recentUsers'));
    }
}

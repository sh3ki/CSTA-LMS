<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Teacher\SubjectController as TeacherSubjectController;
use App\Http\Controllers\Teacher\ResourceController as TeacherResourceController;
use App\Http\Controllers\Teacher\TaskController as TeacherTaskController;
use App\Http\Controllers\Teacher\PerformanceController as TeacherPerformanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use Illuminate\Support\Facades\Route;

// ─── Landing Page ────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('landing');

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Profile (all authenticated roles) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile/settings',           [ProfileController::class, 'show'])->name('profile.settings');
    Route::patch('/profile/update',           [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/photo',            [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::delete('/profile/photo',           [ProfileController::class, 'removePhoto'])->name('profile.removePhoto');
    Route::patch('/profile/password',         [ProfileController::class, 'changePassword'])->name('profile.changePassword');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Teachers
    Route::get('/teachers',                        [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers',                       [TeacherController::class, 'store'])->name('teachers.store');
    Route::put('/teachers/{teacher}',              [TeacherController::class, 'update'])->name('teachers.update');
    Route::patch('/teachers/{teacher}/status',     [TeacherController::class, 'toggleStatus'])->name('teachers.toggleStatus');
    Route::patch('/teachers/{teacher}/password',   [TeacherController::class, 'changePassword'])->name('teachers.changePassword');
    Route::delete('/teachers/{teacher}',           [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/import',                [TeacherController::class, 'import'])->name('teachers.import');

    // Students
    Route::get('/students',                        [StudentController::class, 'index'])->name('students.index');
    Route::post('/students',                       [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}',              [StudentController::class, 'update'])->name('students.update');
    Route::patch('/students/{student}/status',     [StudentController::class, 'toggleStatus'])->name('students.toggleStatus');
    Route::patch('/students/{student}/password',   [StudentController::class, 'changePassword'])->name('students.changePassword');
    Route::delete('/students/{student}',           [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/import',                [StudentController::class, 'import'])->name('students.import');

    // Classes
    Route::get('/classes',                         [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes',                        [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}',                 [ClassController::class, 'show'])->name('classes.show');
    Route::put('/classes/{class}',                 [ClassController::class, 'update'])->name('classes.update');
    Route::patch('/classes/{class}/status',        [ClassController::class, 'toggleStatus'])->name('classes.toggleStatus');
    Route::delete('/classes/{class}',              [ClassController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/import',                 [ClassController::class, 'import'])->name('classes.import');

    // Subjects
    Route::get('/subjects',                        [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects',                       [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}',              [SubjectController::class, 'show'])->name('subjects.show');
    Route::put('/subjects/{subject}',              [SubjectController::class, 'update'])->name('subjects.update');
    Route::patch('/subjects/{subject}/status',     [SubjectController::class, 'toggleStatus'])->name('subjects.toggleStatus');
    Route::delete('/subjects/{subject}',           [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::post('/subjects/import',                [SubjectController::class, 'import'])->name('subjects.import');

    // Resources
    Route::get('/resources',                       [AdminResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources',                      [AdminResourceController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}',            [AdminResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}',         [AdminResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('/resources/{resource}/download',   [AdminResourceController::class, 'download'])->name('resources.download');

    // Tasks
    Route::get('/tasks',                           [AdminTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks',                          [AdminTaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}',                    [AdminTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',                 [AdminTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{task}/download',           [AdminTaskController::class, 'downloadAttachment'])->name('tasks.download');

    // Announcements
    Route::get('/announcements',                   [AnnouncementController::class, 'index'])->name('announcements.index');

    // Reports
    Route::get('/reports',                         [ReportController::class, 'index'])->name('reports.index');

    // Audit Logs
    Route::get('/audit-logs',                      [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Settings
    Route::get('/settings',                        [SettingsController::class, 'index'])->name('settings.index');
});

// ─── Teacher Routes ────────────────────────────────────────────────────────────
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');

    // Classes
    Route::get('/classes', [TeacherClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}', [TeacherClassController::class, 'show'])->name('classes.show');
    Route::post('/classes', [TeacherClassController::class, 'store'])->name('classes.store');
    Route::put('/classes/{class}', [TeacherClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [TeacherClassController::class, 'destroy'])->name('classes.destroy');

    // Subjects Assigned
    Route::get('/subjects',              [TeacherSubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects',             [TeacherSubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}',    [TeacherSubjectController::class, 'show'])->name('subjects.show');

    // Resources Management
    Route::get('/resources',              [TeacherResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources',             [TeacherResourceController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}',   [TeacherResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}',[TeacherResourceController::class, 'destroy'])->name('resources.destroy');
    Route::get('/resources/{resource}/download', [TeacherResourceController::class, 'download'])->name('resources.download');

    // Task Management
    Route::get('/tasks',                  [TeacherTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks',                 [TeacherTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}',           [TeacherTaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}',           [TeacherTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',        [TeacherTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{task}/download',  [TeacherTaskController::class, 'downloadAttachment'])->name('tasks.download');
    Route::patch('/submissions/{submission}/toggle-resubmit', [TeacherTaskController::class, 'toggleSubmissionResubmit'])->name('submissions.toggleResubmit');
    Route::patch('/submissions/{submission}/grade', [TeacherTaskController::class, 'grade'])->name('submissions.grade');
    Route::get('/submissions/{submission}/download', [TeacherTaskController::class, 'downloadSubmission'])->name('submissions.download');
    Route::get('/submission-histories/{history}/download', [TeacherTaskController::class, 'downloadSubmissionHistory'])->name('submission-histories.download');

    // Performance Report
    Route::get('/performance',            [TeacherPerformanceController::class, 'index'])->name('performance.index');
});

// ─── Student Routes ────────────────────────────────────────────────────────────
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    Route::get('/subjects', [StudentSubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects/join', [StudentSubjectController::class, 'joinByCode'])->name('subjects.join');
    Route::get('/subjects/{subject}', [StudentSubjectController::class, 'show'])->name('subjects.show');

    Route::get('/tasks', [StudentTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StudentTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('tasks.submit');
    Route::get('/tasks/{task}/download', [StudentTaskController::class, 'downloadAttachment'])->name('tasks.download');
    Route::get('/resources/{resource}/download', [StudentSubjectController::class, 'downloadResource'])->name('resources.download');

    Route::get('/announcements', [StudentAnnouncementController::class, 'index'])->name('announcements.index');
});

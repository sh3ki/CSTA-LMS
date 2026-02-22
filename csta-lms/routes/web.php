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
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use Illuminate\Support\Facades\Route;

// ─── Landing Page ────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('landing');

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
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
    Route::delete('/classes/{class}',              [ClassController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/import',                 [ClassController::class, 'import'])->name('classes.import');

    // Subjects
    Route::get('/subjects',                        [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects',                       [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}',              [SubjectController::class, 'show'])->name('subjects.show');
    Route::put('/subjects/{subject}',              [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}',           [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::post('/subjects/import',                [SubjectController::class, 'import'])->name('subjects.import');

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
});

// ─── Student Routes ────────────────────────────────────────────────────────────
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
});

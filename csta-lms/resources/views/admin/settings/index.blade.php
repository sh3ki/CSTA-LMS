@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#800020;">settings</span>
            System Settings
        </h1>
        <p class="page-subtitle">Configure application preferences and system options.</p>
    </div>
</div>

@include('partials._toasts')

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf @method('PUT')

    <div class="row g-4">
        <!-- School Information -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="material-icons me-2" style="font-size:18px;color:#800020;">school</span>
                    <span style="font-weight:600;">School Information</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control" value="{{ $settings['school_name'] ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">System Subtitle</label>
                        <input type="text" name="system_subtitle" class="form-control" value="{{ $settings['system_subtitle'] ?? '' }}" placeholder="e.g., Learning Management System">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">School Address</label>
                        <textarea name="school_address" class="form-control" rows="2">{{ $settings['school_address'] ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="school_contact" class="form-control" value="{{ $settings['school_contact'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="school_email" class="form-control" value="{{ $settings['school_email'] ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Settings -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <span class="material-icons me-2" style="font-size:18px;color:#800020;">menu_book</span>
                    <span style="font-weight:600;">Academic Settings</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control" value="{{ $settings['academic_year'] ?? '' }}" placeholder="e.g., 2024-2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Semester <span class="text-danger">*</span></label>
                        <select name="current_semester" class="form-select" required>
                            <option value="1st" @selected(($settings['current_semester'] ?? '') === '1st')>1st Semester</option>
                            <option value="2nd" @selected(($settings['current_semester'] ?? '') === '2nd')>2nd Semester</option>
                            <option value="3rd" @selected(($settings['current_semester'] ?? '') === '3rd')>3rd Semester</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Grading Scale (Total Points)</label>
                        <input type="number" name="grading_scale" class="form-control" value="{{ $settings['grading_scale'] ?? 100 }}" min="10" max="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Passing Grade (%)</label>
                        <input type="number" name="passing_grade" class="form-control" value="{{ $settings['passing_grade'] ?? 75 }}" min="0" max="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Max File Upload Size (MB)</label>
                        <input type="number" name="max_file_size_mb" class="form-control" value="{{ $settings['max_file_size_mb'] ?? 50 }}" min="1" max="500">
                    </div>
                </div>
            </div>
        </div>

        <!-- System Options -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="material-icons me-2" style="font-size:18px;color:#800020;">tune</span>
                    <span style="font-weight:600;">System Options</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="allow_late_submit" id="allowLate" value="1"
                                    {{ ($settings['allow_late_submit'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="allowLate">
                                    <strong>Allow Late Submissions</strong>
                                    <div style="font-size:12px;color:#5f6368;">Students can submit tasks after the due date</div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance" value="1"
                                    {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance">
                                    <strong>Maintenance Mode</strong>
                                    <div style="font-size:12px;color:#5f6368;">Only admins can access the system</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary px-5" style="background:#800020;border-color:#800020;">
            <span class="material-icons align-middle me-1" style="font-size:18px;">save</span>
            Save Settings
        </button>
    </div>
</form>
@endsection

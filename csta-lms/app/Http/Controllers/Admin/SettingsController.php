<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::allSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'      => 'required|string|max:255',
            'system_subtitle'  => 'nullable|string|max:255',
            'academic_year'    => 'required|string|max:20',
            'current_semester' => 'required|in:1st,2nd,3rd',
            'grading_scale'    => 'required|integer|min:10|max:1000',
            'passing_grade'    => 'required|integer|min:0|max:100',
            'school_address'   => 'nullable|string|max:500',
            'school_contact'   => 'nullable|string|max:100',
            'school_email'     => 'nullable|email|max:255',
            'max_file_size_mb' => 'required|integer|min:1|max:500',
            'allow_late_submit'=> 'nullable',
            'maintenance_mode' => 'nullable',
        ]);

        $fields = [
            'school_name', 'system_subtitle', 'academic_year', 'current_semester',
            'grading_scale', 'passing_grade', 'school_address', 'school_contact',
            'school_email', 'max_file_size_mb',
        ];

        foreach ($fields as $field) {
            SystemSetting::set($field, $request->input($field, ''));
        }

        SystemSetting::set('allow_late_submit', $request->has('allow_late_submit') ? '1' : '0');
        SystemSetting::set('maintenance_mode',  $request->has('maintenance_mode')  ? '1' : '0');

        AuditLog::record('settings_update', 'Updated system settings.');

        return back()->with('success', 'Settings saved successfully.');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            ['key' => 'school_name',       'value' => 'Colegio De Sta. Teresa De Avila'],
            ['key' => 'system_subtitle',   'value' => 'Learning Management System'],
            ['key' => 'academic_year',     'value' => '2025-2026'],
            ['key' => 'current_semester',  'value' => '1st'],
            ['key' => 'grading_scale',     'value' => '100'],
            ['key' => 'passing_grade',     'value' => '75'],
            ['key' => 'maintenance_mode',  'value' => '0'],
            ['key' => 'school_address',    'value' => ''],
            ['key' => 'school_contact',    'value' => ''],
            ['key' => 'school_email',      'value' => ''],
            ['key' => 'max_file_size_mb',  'value' => '50'],
            ['key' => 'allow_late_submit', 'value' => '1'],
        ];

        foreach ($defaults as $setting) {
            DB::table('system_settings')->insertOrIgnore(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

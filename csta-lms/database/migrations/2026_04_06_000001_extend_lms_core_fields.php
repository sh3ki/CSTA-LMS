<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->unique()->after('id_number');
            });
        }

        if (!Schema::hasColumn('users', 'course')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('course')->nullable()->after('role');
            });
        }

        if (!Schema::hasColumn('users', 'year_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('year_level')->nullable()->after('course');
            });
        }

        if (!Schema::hasColumn('classes', 'status')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('teacher_id');
            });
        }

        if (!Schema::hasColumn('subjects', 'subject_code')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('subject_code')->nullable()->unique()->after('name');
            });
        }

        if (!Schema::hasColumn('subjects', 'course_code')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('course_code')->nullable()->after('subject_code');
            });
        }

        if (!Schema::hasColumn('subjects', 'semester')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->enum('semester', ['1st', '2nd', '3rd'])->nullable()->after('course_code');
            });
        }

        if (!Schema::hasColumn('subjects', 'status')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('description');
            });
        }

        if (!Schema::hasColumn('subjects', 'created_by')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('tasks', 'task_type')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('task_type')->default('Assignment')->after('title');
            });
        }

        if (!Schema::hasColumn('resources', 'resource_type')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->string('resource_type')->default('Others')->after('title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('resource_type');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_type');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['subject_code', 'course_code', 'semester', 'status']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['email', 'course', 'year_level']);
        });
    }
};

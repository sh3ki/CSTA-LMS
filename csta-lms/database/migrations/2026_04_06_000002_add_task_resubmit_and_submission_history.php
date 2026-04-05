<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('allow_resubmit')->default(false)->after('description');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->text('submission_note')->nullable()->after('file_name');
        });

        Schema::create('submission_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->string('file_path');
            $table->string('file_name');
            $table->text('submission_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_histories');

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('submission_note');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('allow_resubmit');
        });
    }
};

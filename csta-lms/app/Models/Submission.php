<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    public const STATUS_ON_TIME = 'submitted_on_time';
    public const STATUS_LATE = 'submitted_late';
    public const STATUS_MISSING = 'missing';

    protected $fillable = [
        'task_id',
        'student_id',
        'file_path',
        'file_name',
        'submission_note',
        'allow_resubmit',
        'grade',
        'feedback',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'allow_resubmit' => 'boolean',
            'grade'        => 'decimal:2',
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function histories()
    {
        return $this->hasMany(SubmissionHistory::class)->orderByDesc('attempt_number');
    }

    public static function statusFor(?self $submission, Task $task): string
    {
        if (!$submission || !$submission->submitted_at) {
            return self::STATUS_MISSING;
        }

        if ($task->due_date && $submission->submitted_at->gt($task->due_date)) {
            return self::STATUS_LATE;
        }

        return self::STATUS_ON_TIME;
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_ON_TIME => 'Submitted On time',
            self::STATUS_LATE => 'Submitted Late',
            default => 'Missing',
        };
    }

    public static function statusColors(string $status): array
    {
        return match ($status) {
            self::STATUS_ON_TIME => ['background' => '#e6f4ea', 'text' => '#34a853'],
            self::STATUS_LATE => ['background' => '#fef7e0', 'text' => '#f9ab00'],
            default => ['background' => '#fce8e6', 'text' => '#ea4335'],
        };
    }
}

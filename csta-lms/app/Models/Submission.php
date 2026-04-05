<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
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
}

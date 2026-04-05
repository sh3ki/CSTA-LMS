<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'subject_code',
        'course_code',
        'semester',
        'class_id',
        'description',
        'status',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subject $subject) {
            if (empty($subject->subject_code)) {
                $subject->subject_code = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'CLS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (self::where('subject_code', $code)->exists());

        return $code;
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

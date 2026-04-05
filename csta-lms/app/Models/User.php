<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_number',
        'email',
        'full_name',
        'contact_number',
        'profile_picture',
        'role',
        'course',
        'year_level',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status'   => 'boolean',
        ];
    }

    // Auth: use id_number as username
    public function getAuthIdentifierName()
    {
        return 'id_number';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Relationships
    public function taughtClasses()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function enrolledClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function uploadedResources()
    {
        return $this->hasMany(Resource::class, 'uploaded_by');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }
}


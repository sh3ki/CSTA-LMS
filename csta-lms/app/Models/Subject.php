<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'class_id', 'description'];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'role', 'action', 'description', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record(string $action, string $description = '', $user = null)
    {
        $currentUser = $user ?? auth()->user();
        self::create([
            'user_id'     => $currentUser?->id,
            'role'        => $currentUser?->role,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsNotification extends Model
{
    protected $table = 'lms_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'url',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // ── Static helpers ─────────────────────────────────────────────────────────

    public static function send(int $userId, string $type, string $title, string $message, string $icon = 'notifications', ?string $url = null): void
    {
        // Keep only latest 20 per user — remove oldest if needed
        $count = self::where('user_id', $userId)->count();
        if ($count >= 20) {
            $oldest = self::where('user_id', $userId)
                ->orderBy('created_at')
                ->first();
            if ($oldest) {
                $oldest->delete();
            }
        }

        self::create([
            'user_id'  => $userId,
            'type'     => $type,
            'title'    => $title,
            'message'  => $message,
            'icon'     => $icon,
            'url'      => $url,
            'read_at'  => null,
        ]);
    }

    public static function sendToRole(string $role, string $type, string $title, string $message, string $icon = 'notifications', ?string $url = null): void
    {
        User::where('role', $role)->where('status', true)->each(function ($user) use ($type, $title, $message, $icon, $url) {
            self::send($user->id, $type, $title, $message, $icon, $url);
        });
    }

    public static function sendToAll(string $type, string $title, string $message, string $icon = 'notifications', ?string $url = null): void
    {
        User::where('status', true)->each(function ($user) use ($type, $title, $message, $icon, $url) {
            self::send($user->id, $type, $title, $message, $icon, $url);
        });
    }

    public static function unreadCountFor(int $userId): int
    {
        return self::where('user_id', $userId)->whereNull('read_at')->count();
    }

    public static function recentFor(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}

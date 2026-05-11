<?php

namespace App\Http\Controllers;

use App\Models\LmsNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function recent(Request $request)
    {
        $notifications = LmsNotification::recentFor($request->user()->id, 20);
        $unread = $notifications->whereNull('read_at')->count();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'icon'       => $n->icon,
                    'url'        => $n->url,
                    'read_at'    => $n->read_at,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => $unread,
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => LmsNotification::unreadCountFor($request->user()->id),
        ]);
    }

    public function markRead(Request $request, int $id)
    {
        LmsNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        LmsNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

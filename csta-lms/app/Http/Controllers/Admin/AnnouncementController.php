<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\LmsNotification;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('author')->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%$q%")
                   ->orWhere('body', 'like', "%$q%");
            });
        }

        if ($request->filled('target_role')) {
            $query->where('target_role', $request->target_role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->whereNotNull('published_at')->where('published_at', '<=', now());
            } elseif ($request->status === 'draft') {
                $query->whereNull('published_at');
            }
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'target_role' => 'required|in:all,teacher,student',
        ]);

        $announcement = Announcement::create([
            'title'       => $request->title,
            'body'        => $request->body,
            'target_role' => $request->target_role,
            'created_by'  => auth()->id(),
            'published_at' => $request->publish_now ? now() : null,
        ]);

        if ($request->publish_now) {
            $this->notifyUsers($announcement);
        }

        AuditLog::record('announcement_create', "Created announcement: {$announcement->title}");

        return back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'target_role' => 'required|in:all,teacher,student',
        ]);

        $announcement->update([
            'title'       => $request->title,
            'body'        => $request->body,
            'target_role' => $request->target_role,
        ]);

        AuditLog::record('announcement_update', "Updated announcement: {$announcement->title}");

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function publish(Announcement $announcement)
    {
        if ($announcement->published_at) {
            return back()->with('error', 'Announcement is already published.');
        }

        $announcement->update(['published_at' => now()]);
        $this->notifyUsers($announcement);

        AuditLog::record('announcement_publish', "Published announcement: {$announcement->title}");

        return back()->with('success', 'Announcement published and users notified.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        AuditLog::record('announcement_delete', "Deleted announcement: {$title}");

        return back()->with('success', 'Announcement deleted.');
    }

    private function notifyUsers(Announcement $announcement): void
    {
        $roles = match ($announcement->target_role) {
            'teacher' => ['teacher'],
            'student' => ['student'],
            default   => ['teacher', 'student'],
        };

        foreach ($roles as $role) {
            $roleUrl = $role === 'teacher' ? route('teacher.announcements.index') : route('student.announcements.index');
            LmsNotification::sendToRole(
                $role,
                'info',
                'New Announcement',
                $announcement->title,
                'campaign',
                $roleUrl
            );
        }
    }
}

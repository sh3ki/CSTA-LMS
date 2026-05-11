<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\LmsNotification;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        // Teacher sees announcements targeted at teachers or all, plus their own created
        $query = Announcement::with('author')
            ->where(function ($q) use ($teacher) {
                $q->whereIn('target_role', ['teacher', 'all'])
                  ->orWhere('created_by', $teacher->id);
            })
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")->orWhere('body', 'like', "%$s%");
            });
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('teacher.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title'        => $request->title,
            'body'         => $request->body,
            'target_role'  => 'student',
            'created_by'   => auth()->id(),
            'published_at' => now(),
        ]);

        // Notify students
        LmsNotification::sendToRole('student', 'info', 'New Announcement', $announcement->title, 'campaign', route('student.announcements.index'));

        AuditLog::record('announcement_create', "Teacher created announcement: {$announcement->title}");

        return back()->with('success', 'Announcement posted to students.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeOwner($announcement);

        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $announcement->update([
            'title' => $request->title,
            'body'  => $request->body,
        ]);

        AuditLog::record('announcement_update', "Teacher updated announcement: {$announcement->title}");

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeOwner($announcement);

        $title = $announcement->title;
        $announcement->delete();

        AuditLog::record('announcement_delete', "Teacher deleted announcement: {$title}");

        return back()->with('success', 'Announcement deleted.');
    }

    private function authorizeOwner(Announcement $announcement): void
    {
        if ($announcement->created_by !== auth()->id()) {
            abort(403);
        }
    }
}

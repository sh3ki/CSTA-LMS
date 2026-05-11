<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('author')
            ->published()
            ->forRole('student')
            ->latest('published_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")->orWhere('body', 'like', "%$s%");
            });
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('student.announcements.index', compact('announcements'));
    }
}
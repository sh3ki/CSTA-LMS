<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index');
    }
}

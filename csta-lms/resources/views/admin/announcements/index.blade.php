@extends('layouts.admin')
@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#1a73e8;">campaign</span>
            Announcements
        </h1>
        <p class="page-subtitle">Post and manage school announcements.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2" style="background:#1a73e8;border-color:#1a73e8;">
        <span class="material-icons" style="font-size:18px;">add</span>
        New Announcement
    </button>
</div>

<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:80px;height:80px;background:#e8f0fe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:40px;color:#1a73e8;">campaign</span>
        </div>
        <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Announcements</h5>
        <p style="font-size:14px;color:#5f6368;line-height:1.6;">
            This page is under development. Announcement management — create, publish,
            and notify teachers and students — will be available here.
        </p>
    </div>
</div>
@endsection

@extends('layouts.student')
@section('title', 'Announcements')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">campaign</span>
            Announcements
        </h1>
        <p class="page-subtitle">Updates shared by your teachers will appear here.</p>
    </div>
</div>

<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:80px;height:80px;background:#fff4cc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:40px;color:#f9ab00;">campaign</span>
        </div>
        <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Announcements</h5>
        <p style="font-size:14px;color:#5f6368;line-height:1.6;">
            This section is ready for class announcements once the publishing flow is connected.
            For now, use your subject pages and task feeds for class updates.
        </p>
    </div>
</div>
@endsection
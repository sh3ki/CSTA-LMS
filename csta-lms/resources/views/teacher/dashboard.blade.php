@extends('layouts.teacher')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#34a853;">dashboard</span>
            Teacher Dashboard
        </h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}!</p>
    </div>
</div>

<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:96px;height:96px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <span class="material-icons" style="font-size:48px;color:#fff;">school</span>
        </div>
        <h3 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">
            Teacher Panel
        </h3>
        <p style="font-size:15px;color:#5f6368;line-height:1.6;">
            This panel is under development. Subject management, task management,
            performance reports, and announcements will be available here.
        </p>
    </div>
</div>
@endsection

@extends('layouts.student')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#f9ab00;">dashboard</span>
            Student Dashboard
        </h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->full_name }}!</p>
    </div>
</div>

<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:96px;height:96px;background:linear-gradient(135deg,#f9ab00,#fbbc04);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <span class="material-icons" style="font-size:48px;color:#fff;">school</span>
        </div>
        <h3 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">
            Student Panel
        </h3>
        <p style="font-size:15px;color:#5f6368;line-height:1.6;">
            This panel is under development. Your subjects, announcements,
            and tasks will be visible here once your teacher posts them.
        </p>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#1a73e8;">settings</span>
            System Settings
        </h1>
        <p class="page-subtitle">Configure application preferences and system options.</p>
    </div>
</div>

<div class="card p-5 text-center">
    <div style="max-width:480px;margin:0 auto;">
        <div style="width:80px;height:80px;background:#e8f0fe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span class="material-icons" style="font-size:40px;color:#1a73e8;">settings</span>
        </div>
        <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">System Settings</h5>
        <p style="font-size:14px;color:#5f6368;line-height:1.6;">
            This page is under development. School information, grading configurations,
            semester setup, and notification preferences will be configurable here.
        </p>
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &mdash; CSTA-LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #1557b0;
            --sidebar-w: 260px;
            --navbar-h: 64px;
            --bg: #f1f3f4;
            --text-main: #202124;
            --text-muted: #5f6368;
            --border: #e8eaed;
            --sidebar-text: #3c4043;
            --sidebar-active-bg: #e8f0fe;
            --sidebar-active: #1a73e8;
        }

        * { font-family: 'Roboto', sans-serif; box-sizing: border-box; }
        body { background: var(--bg); margin: 0; padding: 0; min-height: 100vh; }

        /* ── Top Navbar ── */
        .app-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--navbar-h);
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.12);
            display: flex;
            align-items: center;
            padding: 0 16px 0 12px;
            z-index: 1000;
            gap: 8px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: calc(var(--sidebar-w) - 12px);
            text-decoration: none;
        }

        .nav-brand-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #1a73e8, #34a853);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .nav-brand-text {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #3c4043;
            line-height: 1.3;
        }

        .nav-brand-sub {
            font-size: 10px;
            color: #5f6368;
        }

        .hamburger-btn {
            background: none;
            border: none;
            color: #5f6368;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            display: flex; align-items: center;
            transition: background .2s;
        }

        .hamburger-btn:hover { background: #f1f3f4; }

        .nav-spacer { flex: 1; }

        .nav-actions { display: flex; align-items: center; gap: 4px; }

        .nav-icon-btn {
            background: none;
            border: none;
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #5f6368;
            cursor: pointer;
            transition: background .2s;
            position: relative;
        }

        .nav-icon-btn:hover { background: #f1f3f4; }

        .notif-badge {
            position: absolute;
            top: 6px; right: 6px;
            width: 10px; height: 10px;
            background: #ea4335;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 24px;
            transition: background .2s;
        }

        .profile-btn:hover { background: #f1f3f4; }

        .avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #1a73e8, #8ab4f8);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
            overflow: hidden;
        }
        .avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }

        .profile-info {
            text-align: left;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 500;
            color: #202124;
            line-height: 1.3;
        }

        .profile-id {
            font-size: 11px;
            color: #5f6368;
        }

        /* ── Sidebar ── */
        .app-sidebar {
            position: fixed;
            top: var(--navbar-h);
            left: 0;
            width: var(--sidebar-w);
            height: calc(100vh - var(--navbar-h));
            background: #fff;
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 12px 8px;
            transition: transform .3s ease;
            z-index: 900;
        }

        .app-sidebar.collapsed {
            transform: translateX(calc(-1 * var(--sidebar-w)));
        }

        .sidebar-section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #80868b;
            padding: 12px 16px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 16px;
            border-radius: 24px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            transition: all .15s;
            margin-bottom: 2px;
        }

        .sidebar-link:hover {
            background: #f1f3f4;
            color: var(--text-main);
        }

        .sidebar-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active);
            font-weight: 600;
        }

        .sidebar-link.active .material-icons {
            color: var(--sidebar-active);
        }

        .sidebar-link .material-icons {
            font-size: 20px;
            color: #5f6368;
            flex-shrink: 0;
        }

        .sidebar-link.active .material-icons { color: var(--sidebar-active); }

        .sidebar-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 0;
        }

        /* ── Main Content ── */
        .app-main {
            margin-left: var(--sidebar-w);
            margin-top: var(--navbar-h);
            min-height: calc(100vh - var(--navbar-h));
            padding: 28px;
            transition: margin-left .3s ease;
        }

        .app-main.expanded { margin-left: 0; }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 22px;
            font-weight: 600;
            color: #202124;
            margin: 0;
        }

        .page-subtitle {
            font-size: 13px;
            color: #5f6368;
            margin: 2px 0 0;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            border-radius: 12px 12px 0 0 !important;
            padding: 16px 20px;
        }

        /* ── Stat Card ── */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: box-shadow .2s;
        }

        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon .material-icons { font-size: 26px; }

        .stat-value {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #202124;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: #5f6368;
        }

        /* ── Table ── */
        .table th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #5f6368;
            border-bottom: 2px solid #e8eaed;
            padding: 12px 16px;
            white-space: nowrap;
        }

        .table td {
            padding: 13px 16px;
            font-size: 14px;
            color: #202124;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr:hover { background: #f8f9fa; }
        .table tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge-active {
            background: #e6f4ea;
            color: #137333;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-inactive {
            background: #fce8e6;
            color: #c5221f;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Buttons */
        .btn-primary {
            background: #1a73e8;
            border-color: #1a73e8;
            font-weight: 500;
        }

        .btn-primary:hover { background: #1557b0; border-color: #1557b0; }

        .btn-icon {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-icon:hover { background: #f1f3f4; }
        .btn-icon .material-icons { font-size: 18px; }

        /* Search bar */
        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f3f4;
            border-radius: 24px;
            padding: 0 16px;
            gap: 8px;
            height: 40px;
        }

        .search-bar input {
            background: none;
            border: none;
            outline: none;
            font-size: 14px;
            color: #202124;
            width: 220px;
        }

        .search-bar input::placeholder { color: #5f6368; }

        /* Dropdown */
        .dropdown-item { font-size: 14px; padding: 8px 16px; }
        .dropdown-item:hover { background: #f1f3f4; }

        /* Modal */
        .modal-header {
            border-bottom: 1px solid #e8eaed;
            padding: 20px 24px 16px;
        }

        .modal-title {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #202124;
        }

        .modal-body { padding: 24px; }
        .modal-footer { border-top: 1px solid #e8eaed; padding: 16px 24px; }

        .form-control, .form-select {
            border: 1.5px solid #dadce0;
            border-radius: 8px;
            font-size: 14px;
            color: #202124;
            transition: border-color .2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.15);
        }

        .form-label { font-size: 13px; font-weight: 500; color: #3c4043; margin-bottom: 6px; }

        /* Pagination */
        .pagination .page-link {
            color: #1a73e8;
            border: 1px solid #e8eaed;
            border-radius: 8px !important;
            margin: 0 2px;
            font-size: 14px;
        }

        .pagination .page-item.active .page-link {
            background: #1a73e8;
            border-color: #1a73e8;
        }

        /* Toast */
        .toast-container { z-index: 9999; }

        /* Alert slim */
        .alert-slim {
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success.alert-slim { background: #e6f4ea; border: none; color: #137333; }
        .alert-danger.alert-slim  { background: #fce8e6; border: none; color: #c5221f; }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 899;
        }

        @media (max-width: 992px) {
            .app-sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            .app-sidebar.open { transform: translateX(0); }
            .app-main { margin-left: 0; }
            .sidebar-overlay.open { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ── Top Navbar ── -->
<header class="app-navbar">
    <button class="hamburger-btn me-2" id="sidebarToggle">
        <span class="material-icons">menu</span>
    </button>

    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <img src="{{ asset('logo.jpg') }}" alt="CSTA-LMS" style="height:40px;width:auto;object-fit:contain;border-radius:6px;">
        <div>
            <div class="nav-brand-text">CSTA-LMS</div>
            <div class="nav-brand-sub">Admin Panel</div>
        </div>
    </a>

    <div class="nav-spacer"></div>

    <div class="nav-actions">
        <button class="nav-icon-btn" title="Notifications">
            <span class="material-icons">notifications</span>
            <span class="notif-badge"></span>
        </button>

        <div class="dropdown">
            <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" style="border:none;">
                <div class="avatar">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                    @endif
                </div>
                <div class="profile-info d-none d-md-block">
                    <div class="profile-name">{{ auth()->user()->full_name }}</div>
                    <div class="profile-id">{{ auth()->user()->id_number }}</div>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-radius:12px;min-width:220px;padding:8px;">
                <li>
                    <div class="px-3 py-2 border-bottom">
                        <div class="fw-medium" style="font-size:14px;">{{ auth()->user()->full_name }}</div>
                        <div style="font-size:12px;color:#5f6368;">{{ auth()->user()->id_number }} &bull; Admin</div>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item rounded" href="{{ route('profile.settings') }}" style="margin-top:4px;">
                        <span class="material-icons align-middle me-2" style="font-size:16px;">manage_accounts</span>
                        Profile Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider mx-2"></li>
                <li>
                    <button class="dropdown-item rounded text-danger" style="background:none;border:none;width:100%;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <span class="material-icons align-middle me-2" style="font-size:16px;">logout</span>
                        Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- ── Sidebar Overlay ── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Sidebar ── -->
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-section-label">Main</div>

    <a href="{{ route('admin.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="material-icons">dashboard</span>
        Dashboard
    </a>

    <div class="sidebar-divider"></div>
    <div class="sidebar-section-label">Management</div>

    <a href="{{ route('admin.teachers.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
        <span class="material-icons">person_outline</span>
        Teachers
    </a>

    <a href="{{ route('admin.students.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
        <span class="material-icons">school</span>
        Students
    </a>

    <a href="{{ route('admin.classes.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.classes*') ? 'active' : '' }}">
        <span class="material-icons">class</span>
        Classes
    </a>

    <a href="{{ route('admin.subjects.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
        <span class="material-icons">menu_book</span>
        Subjects
    </a>

    <div class="sidebar-divider"></div>
    <div class="sidebar-section-label">Communication</div>

    <a href="{{ route('admin.announcements.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
        <span class="material-icons">campaign</span>
        Announcements
    </a>

    <div class="sidebar-divider"></div>
    <div class="sidebar-section-label">System</div>

    <a href="{{ route('admin.reports.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
        <span class="material-icons">bar_chart</span>
        Reports
    </a>

    <a href="{{ route('admin.audit-logs.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
        <span class="material-icons">history</span>
        Audit Logs
    </a>

    <a href="{{ route('admin.settings.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
        <span class="material-icons">settings</span>
        Settings
    </a>
</aside>

<!-- ── Main Content ── -->
<main class="app-main" id="appMain">

    @yield('content')
</main>

<!-- ── Logout Modal ── -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-body text-center p-5">
                <div style="width:64px;height:64px;background:#fce8e6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span class="material-icons" style="font-size:32px;color:#ea4335;">logout</span>
                </div>
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:8px;">Sign Out?</h5>
                <p style="font-size:14px;color:#5f6368;margin-bottom:24px;">Are you sure you want to sign out of your account?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <span class="material-icons align-middle me-1" style="font-size:16px;">logout</span>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('appSidebar');
    const main     = document.getElementById('appMain');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    let collapsed = false;

    function isMobile() { return window.innerWidth <= 992; }

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        } else {
            collapsed = !collapsed;
            sidebar.classList.toggle('collapsed', collapsed);
            main.classList.toggle('expanded', collapsed);
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });

</script>
@include('partials._toasts')
@stack('scripts')
</body>
</html>

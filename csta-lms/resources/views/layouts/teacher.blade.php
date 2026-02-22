<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher') &mdash; CSTA-LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8;
            --sidebar-w: 260px;
            --navbar-h: 64px;
            --bg: #f1f3f4;
            --border: #e8eaed;
        }
        * { font-family: 'Roboto', sans-serif; box-sizing: border-box; }
        body { background: var(--bg); margin: 0; padding: 0; min-height: 100vh; }

        .app-navbar {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--navbar-h); background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.12);
            display: flex; align-items: center;
            padding: 0 16px 0 12px; z-index: 1000; gap: 8px;
        }

        .nav-brand { display:flex; align-items:center; gap:10px; min-width:calc(var(--sidebar-w) - 12px); text-decoration:none; }
        .avatar { overflow:hidden; }
        .avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
        .nav-brand-text { font-family:'Google Sans',Roboto,sans-serif;font-size:14px;font-weight:600;color:#3c4043;line-height:1.3; }
        .nav-brand-sub { font-size:10px;color:#5f6368; }
        .hamburger-btn { background:none;border:none;color:#5f6368;padding:8px;border-radius:50%;cursor:pointer;display:flex;align-items:center;transition:background .2s; }
        .hamburger-btn:hover { background:#f1f3f4; }
        .nav-spacer { flex:1; }
        .nav-icon-btn { background:none;border:none;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#5f6368;cursor:pointer;transition:background .2s;position:relative; }
        .nav-icon-btn:hover { background:#f1f3f4; }
        .notif-badge { position:absolute;top:6px;right:6px;width:10px;height:10px;background:#ea4335;border-radius:50%;border:2px solid #fff; }
        .profile-btn { display:flex;align-items:center;gap:10px;background:none;border:none;cursor:pointer;padding:6px 12px;border-radius:24px;transition:background .2s; }
        .profile-btn:hover { background:#f1f3f4; }
        .avatar { width:36px;height:36px;background:linear-gradient(135deg,#34a853,#81c995);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Google Sans',Roboto,sans-serif;font-size:14px;font-weight:600;flex-shrink:0; }
        .profile-name { font-size:14px;font-weight:500;color:#202124;line-height:1.3; }
        .profile-id { font-size:11px;color:#5f6368; }

        .app-sidebar { position:fixed;top:var(--navbar-h);left:0;width:var(--sidebar-w);height:calc(100vh - var(--navbar-h));background:#fff;border-right:1px solid var(--border);overflow-y:auto;padding:12px 8px;transition:transform .3s ease;z-index:900; }
        .app-sidebar.collapsed { transform:translateX(calc(-1 * var(--sidebar-w))); }
        .sidebar-section-label { font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#80868b;padding:12px 16px 6px; }
        .sidebar-link { display:flex;align-items:center;gap:14px;padding:10px 16px;border-radius:24px;color:#3c4043;text-decoration:none;font-size:14px;font-weight:400;transition:all .15s;margin-bottom:2px; }
        .sidebar-link:hover { background:#f1f3f4;color:#202124; }
        .sidebar-link.active { background:#e6f4ea;color:#34a853;font-weight:600; }
        .sidebar-link.active .material-icons { color:#34a853; }
        .sidebar-link .material-icons { font-size:20px;color:#5f6368;flex-shrink:0; }
        .sidebar-divider { height:1px;background:var(--border);margin:8px 0; }

        .app-main { margin-left:var(--sidebar-w);margin-top:var(--navbar-h);min-height:calc(100vh - var(--navbar-h));padding:28px;transition:margin-left .3s ease; }
        .app-main.expanded { margin-left:0; }
        .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px; }
        .page-title { font-family:'Google Sans',Roboto,sans-serif;font-size:22px;font-weight:600;color:#202124;margin:0; }
        .page-subtitle { font-size:13px;color:#5f6368;margin:2px 0 0; }
        .card { border:1px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .alert-slim { border-radius:8px;padding:10px 16px;font-size:14px;display:flex;align-items:center;gap:8px; }
        .alert-success.alert-slim { background:#e6f4ea;border:none;color:#137333; }
        .alert-danger.alert-slim { background:#fce8e6;border:none;color:#c5221f; }

        .sidebar-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:899; }

        @media (max-width: 992px) {
            .app-sidebar { transform:translateX(calc(-1 * var(--sidebar-w))); }
            .app-sidebar.open { transform:translateX(0); }
            .app-main { margin-left:0; }
            .sidebar-overlay.open { display:block; }
        }
    </style>
    @stack('styles')
</head>
<body>

<header class="app-navbar">
    <button class="hamburger-btn me-2" id="sidebarToggle">
        <span class="material-icons">menu</span>
    </button>
    <a href="{{ route('teacher.dashboard') }}" class="nav-brand">
        <img src="{{ asset('logo.jpg') }}" alt="CSTA-LMS" style="height:40px;width:auto;object-fit:contain;border-radius:6px;">
        <div>
            <div class="nav-brand-text">CSTA-LMS</div>
            <div class="nav-brand-sub">Teacher Panel</div>
        </div>
    </a>
    <div class="nav-spacer"></div>
    <div class="d-flex align-items-center gap-1">
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
                <div class="d-none d-md-block">
                    <div class="profile-name">{{ auth()->user()->full_name }}</div>
                    <div class="profile-id">{{ auth()->user()->id_number }}</div>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-radius:12px;min-width:220px;padding:8px;">
                <li>
                    <div class="px-3 py-2 border-bottom">
                        <div class="fw-medium" style="font-size:14px;">{{ auth()->user()->full_name }}</div>
                        <div style="font-size:12px;color:#5f6368;">{{ auth()->user()->id_number }} &bull; Teacher</div>
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

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-section-label">Teacher Menu</div>
    <a href="{{ route('teacher.dashboard') }}" class="sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
        <span class="material-icons">dashboard</span>
        Dashboard
    </a>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section-label">Classroom</div>
    <a href="#" class="sidebar-link">
        <span class="material-icons">menu_book</span>
        Subjects Assigned
    </a>
    <a href="#" class="sidebar-link">
        <span class="material-icons">folder_open</span>
        Resources Management
    </a>
    <a href="#" class="sidebar-link">
        <span class="material-icons">assignment</span>
        Task Management
    </a>
    <a href="#" class="sidebar-link">
        <span class="material-icons">bar_chart</span>
        Performance Report
    </a>
    <a href="#" class="sidebar-link">
        <span class="material-icons">campaign</span>
        Announcements
    </a>
</aside>

<main class="app-main" id="appMain">
    @yield('content')
</main>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15);">
            <div class="modal-body text-center p-5">
                <div style="width:64px;height:64px;background:#fce8e6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span class="material-icons" style="font-size:32px;color:#ea4335;">logout</span>
                </div>
                <h5 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;margin-bottom:8px;">Sign Out?</h5>
                <p style="font-size:14px;color:#5f6368;margin-bottom:24px;">Are you sure you want to sign out?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('appSidebar');
    const main    = document.getElementById('appMain');
    const overlay = document.getElementById('sidebarOverlay');
    let collapsed = false;

    document.getElementById('sidebarToggle').addEventListener('click', () => {
        if (window.innerWidth <= 992) {
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

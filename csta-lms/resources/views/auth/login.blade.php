<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &mdash; CSTA-LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { font-family: 'Roboto', sans-serif; box-sizing: border-box; }

        html, body { height: 100%; margin: 0; padding: 0; }

        .login-page {
            display: flex;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #1a73e8 0%, #0d47a1 60%, #083378 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 420px; height: 420px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -140px; left: -80px;
            width: 380px; height: 380px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
        }
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .left-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 52px;
            align-self: flex-start;
            position: relative;
            z-index: 1;
        }
        .left-brand img {
            height: 52px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }
        .left-brand-text .app-name {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }
        .left-brand-text .school-name {
            font-size: 12px;
            color: rgba(255,255,255,.8);
            margin-top: 2px;
        }
        .left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
        }
        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 24px;
            padding: 6px 18px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 28px;
            backdrop-filter: blur(4px);
        }
        .left-content h1 {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 36px;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 16px;
        }
        .left-content p {
            font-size: 15px;
            opacity: .85;
            max-width: 380px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            text-align: left;
            max-width: 340px;
            margin: 0 auto;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            padding: 12px 16px;
            backdrop-filter: blur(4px);
        }
        .feature-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon .material-icons { font-size: 18px; color: #fff; }
        .feature-text strong { display:block; font-size:13px; font-weight:600; color:#fff; }
        .feature-text span   { font-size:12px; opacity:.75; color:#fff; }
        .left-footer {
            position: relative;
            z-index: 1;
            margin-top: 48px;
            font-size: 12px;
            color: rgba(255,255,255,.5);
            text-align: center;
        }

        /* ── RIGHT PANEL ── */
        .login-right {
            width: 480px;
            flex-shrink: 0;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08), 0 1px 3px rgba(0,0,0,.06);
            padding: 44px 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        .login-logo img {
            height: 72px;
            width: auto;
            object-fit: contain;
        }
        .login-card h4 {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
            color: #202124;
            margin-bottom: 4px;
        }
        .login-card .subtitle {
            font-size: 14px;
            color: #5f6368;
            text-align: center;
            margin-bottom: 24px;
        }
        .login-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e8eaed 20%, #e8eaed 80%, transparent);
            margin-bottom: 28px;
        }
        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #5f6368;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
        }
        .form-control {
            border: 1.5px solid #dadce0;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            color: #202124;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
        }
        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,.15);
            outline: none;
        }
        .form-control.is-invalid { border-color: #ea4335; }
        .input-group .form-control { border-right: none; border-radius: 10px 0 0 10px; }
        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #dadce0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .btn-pw-toggle {
            border: 1.5px solid #dadce0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            color: #5f6368;
            padding: 0 14px;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-pw-toggle:hover { background: #f1f3f4; }
        .btn-login {
            background: linear-gradient(135deg, #1a73e8, #1557b0);
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 100%;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Google Sans', Roboto, sans-serif;
            transition: all .2s;
            cursor: pointer;
            letter-spacing: .3px;
            box-shadow: 0 2px 8px rgba(26,115,232,.35);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1557b0, #0d47a1);
            box-shadow: 0 4px 16px rgba(26,115,232,.45);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: scale(.98); box-shadow: none; }
        .form-check-input:checked { background-color: #1a73e8; border-color: #1a73e8; }
        .alert-danger {
            background: #fce8e6;
            border: none;
            border-left: 4px solid #ea4335;
            border-radius: 8px;
            color: #c5221f;
            font-size: 13.5px;
            padding: 12px 16px;
        }
        .alert-success {
            background: #e6f4ea;
            border: none;
            border-left: 4px solid #34a853;
            border-radius: 8px;
            color: #137333;
            font-size: 13.5px;
            padding: 12px 16px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 22px;
            font-size: 13px;
            color: #5f6368;
            text-decoration: none;
            transition: color .15s;
        }
        .back-link:hover { color: #1a73e8; }
        .right-footer {
            position: absolute;
            bottom: 16px;
            font-size: 11px;
            color: #adb5bd;
            text-align: center;
            width: 100%;
        }

        @media (max-width: 900px) {
            .login-page { flex-direction: column; }
            .login-left { padding: 36px 28px; }
            .left-brand  { margin-bottom: 20px; }
            .left-content h1 { font-size: 24px; }
            .left-content p, .feature-list { display: none; }
            .left-footer { margin-top: 24px; }
            .login-right { width: 100%; padding: 32px 20px 60px; }
            .login-card  { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-page">

    {{-- ── LEFT PANEL ── --}}
    <div class="login-left">
        <div class="deco-circle" style="width:200px;height:200px;top:30%;right:-60px;"></div>
        <div class="deco-circle" style="width:120px;height:120px;top:10%;left:40%;"></div>

        <div class="left-brand">
            <img src="{{ asset('logo.jpg') }}" alt="CSTA Logo">
            <div class="left-brand-text">
                <div class="app-name">CSTA-LMS</div>
                <div class="school-name">College De Sta. Teresa De Avila</div>
            </div>
        </div>

        <div class="left-content">
            <div class="welcome-badge">
                <span class="material-icons" style="font-size:15px;">auto_awesome</span>
                Learning Management System
            </div>
            <h1>Empowering<br>Digital Learning</h1>
            <p>A modern platform connecting teachers, students, and administrators — making education more accessible, organized, and effective.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><span class="material-icons">class</span></div>
                    <div class="feature-text">
                        <strong>Class Management</strong>
                        <span>Organize classes, subjects &amp; enrollments</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><span class="material-icons">assignment</span></div>
                    <div class="feature-text">
                        <strong>Task &amp; Activities</strong>
                        <span>Post tasks and track student progress</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><span class="material-icons">campaign</span></div>
                    <div class="feature-text">
                        <strong>Announcements</strong>
                        <span>Broadcast updates to your classes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-footer">
            &copy; {{ date('Y') }} College De Sta. Teresa De Avila. All rights reserved.
        </div>
    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="login-right">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('logo.jpg') }}" alt="CSTA Logo">
            </div>
            <h4>Sign In</h4>
            <p class="subtitle">Use your CSTA account credentials</p>
            <div class="login-divider"></div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success mb-3 d-flex align-items-center gap-2">
                    <span class="material-icons" style="font-size:17px;flex-shrink:0;">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if ($errors->has('id_number'))
                <div class="alert alert-danger mb-3 d-flex align-items-center gap-2">
                    <span class="material-icons" style="font-size:17px;flex-shrink:0;">error</span>
                    {{ $errors->first('id_number') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="id_number" class="form-label">ID Number</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <span class="material-icons" style="font-size:18px;color:#5f6368;">badge</span>
                        </span>
                        <input
                            type="text"
                            id="id_number"
                            name="id_number"
                            class="form-control {{ $errors->has('id_number') ? 'is-invalid' : '' }}"
                            placeholder="Enter your ID number"
                            value="{{ old('id_number') }}"
                            autocomplete="username"
                            required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <span class="material-icons" style="font-size:18px;color:#5f6368;">lock</span>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('id_number') ? 'is-invalid' : '' }}"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            style="border-right:none;border-radius:0;"
                            required>
                        <button type="button" class="btn-pw-toggle" id="togglePassword" tabindex="-1">
                            <span class="material-icons" style="font-size:18px;" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:13px;color:#5f6368;">
                            Remember me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span class="material-icons align-middle me-1" style="font-size:18px;vertical-align:-4px;">login</span>
                    Sign In
                </button>
            </form>
        </div>

        <a href="{{ route('landing') }}" class="back-link">
            <span class="material-icons" style="font-size:15px;">arrow_back</span>
            Back to Home
        </a>

        <div class="right-footer">
            &copy; {{ date('Y') }} CSTA-LMS &mdash; All rights reserved.
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('togglePassword');
    const pwInput   = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');

    toggleBtn.addEventListener('click', () => {
        const isPassword = pwInput.type === 'password';
        pwInput.type     = isPassword ? 'text' : 'password';
        eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
    });
</script>
</body>
</html>

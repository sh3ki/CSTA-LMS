<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSTA-LMS &mdash; College De Sta. Teresa De Avila</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #1557b0;
            --accent: #34a853;
            --danger: #ea4335;
            --warning: #fbbc04;
            --bg: #f8f9fa;
            --sidebar-w: 260px;
            --navbar-h: 64px;
        }

        * { font-family: 'Roboto', sans-serif; }

        /* ── Landing ── */
        .landing-navbar {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.12);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .landing-navbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .landing-navbar .brand img {
            height: 40px;
        }

        .landing-navbar .brand-text {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 16px;
            font-weight: 500;
            color: #3c4043;
            line-height: 1.3;
        }

        .landing-navbar .brand-sub {
            font-size: 11px;
            color: #5f6368;
            font-weight: 400;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            color: #fff;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
        }

        .hero h1 {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 48px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            opacity: .9;
            max-width: 600px;
            margin-bottom: 36px;
        }

        .btn-hero-primary {
            background: #fff;
            color: #1a73e8;
            border: none;
            padding: 14px 32px;
            border-radius: 24px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
            display: inline-block;
        }

        .btn-hero-primary:hover {
            background: #e8f0fe;
            color: #1557b0;
            transform: translateY(-1px);
        }

        .btn-hero-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,.7);
            padding: 12px 30px;
            border-radius: 24px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
            display: inline-block;
        }

        .btn-hero-outline:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
            border-color: #fff;
        }

        /* About */
        .section-about {
            padding: 80px 0;
            background: #fff;
        }

        .section-label {
            display: inline-block;
            background: #e8f0fe;
            color: #1a73e8;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: #202124;
            margin-bottom: 16px;
        }

        /* Features */
        .section-features {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .feature-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px 28px;
            height: 100%;
            border: 1px solid #e8eaed;
            transition: all .25s;
        }

        .feature-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
            transform: translateY(-4px);
            border-color: #d2e3fc;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .feature-icon .material-icons {
            font-size: 28px;
        }

        .feature-card h5 {
            font-family: 'Google Sans', Roboto, sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #202124;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            color: #5f6368;
            line-height: 1.6;
            margin: 0;
        }

        /* Mission Vision */
        .mv-card {
            background: linear-gradient(135deg, #e8f0fe 0%, #f1f8e9 100%);
            border-radius: 16px;
            padding: 36px;
        }

        /* Footer */
        .site-footer {
            background: #202124;
            color: rgba(255,255,255,.7);
            padding: 48px 0 24px;
        }

        .site-footer h6 {
            color: #fff;
            font-family: 'Google Sans', Roboto, sans-serif;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .site-footer a {
            color: rgba(255,255,255,.6);
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
            transition: color .2s;
        }

        .site-footer a:hover { color: #fff; }

        .site-footer .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.1);
            margin-top: 36px;
            padding-top: 20px;
            font-size: 13px;
            text-align: center;
        }

        .scroll-to-top {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 44px;
            height: 44px;
            background: #1a73e8;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(26,115,232,.4);
            transition: all .2s;
            z-index: 9999;
            text-decoration: none;
        }

        .scroll-to-top:hover {
            background: #1557b0;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="landing-navbar" id="top">
    <a href="{{ route('landing') }}" class="brand me-auto">
        <img src="{{ asset('logo.jpg') }}" alt="CSTA-LMS Logo" style="height:44px;width:auto;object-fit:contain;">
        <div>
            <div class="brand-text">CSTA Learning Management System</div>
            <div class="brand-sub">College De Sta. Teresa De Avila</div>
        </div>
    </a>
    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4" style="background:#1a73e8;border:none;font-weight:500;">
        Sign In
    </a>
</nav>

<!-- ── Hero ── -->
<section class="hero" id="home">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1>Empowering Digital Learning at<br>College De Sta. Teresa De Avila</h1>
                <p>A modern, streamlined Learning Management System designed to connect teachers, students, and administrators — making education more accessible and organized.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <span class="material-icons align-middle me-1" style="font-size:18px;">login</span>
                        Get Started
                    </a>
                    <a href="#features" class="btn-hero-outline">Learn More</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center mt-4 mt-lg-0">
                <div style="width:340px;height:280px;background:rgba(255,255,255,.12);border-radius:20px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;padding:28px;">
                    <span class="material-icons" style="font-size:72px;color:rgba(255,255,255,.9);">menu_book</span>
                    <div style="font-family:'Google Sans',Roboto,sans-serif;font-size:18px;font-weight:600;color:rgba(255,255,255,.95);">CSTA-LMS</div>
                    <div style="font-size:13px;color:rgba(255,255,255,.75);text-align:center;">Manage. Learn. Grow.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── About ── -->
<section class="section-about" id="about">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="section-label">About the School</span>
                <h2 class="section-title">College De Sta. Teresa De Avila</h2>
                <p class="text-secondary lh-lg">
                    College De Sta. Teresa De Avila is a premier Catholic educational institution committed to excellence in academics, character formation, and holistic development. Rooted in the Teresian tradition, the college provides quality education that integrates faith, service, and leadership.
                </p>
                <p class="text-secondary lh-lg">
                    Our Learning Management System brings our time-honored academic traditions into the digital age — enabling seamless communication, resource sharing, and performance tracking for our entire academic community.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="mv-card">
                            <h5 class="fw-semibold mb-2" style="color:#1a73e8;">
                                <span class="material-icons align-middle me-1" style="font-size:20px;">visibility</span>
                                Vision
                            </h5>
                            <p class="text-secondary mb-0 lh-lg" style="font-size:14px;">
                                To be a leading institution in Catholic higher education, forming competent, committed, and compassionate graduates who will become agents of transformation in society.
                            </p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mv-card" style="background:linear-gradient(135deg,#e8f5e9 0%,#e3f2fd 100%);">
                            <h5 class="fw-semibold mb-2" style="color:#34a853;">
                                <span class="material-icons align-middle me-1" style="font-size:20px;">rocket_launch</span>
                                Mission
                            </h5>
                            <p class="text-secondary mb-0 lh-lg" style="font-size:14px;">
                                To provide quality and holistic education anchored on Christian values, guided by the spirit of St. Teresa of Avila, and responsive to the needs of the times.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Features ── -->
<section class="section-features" id="features">
    <div class="container">
        <div class="text-center mb-56">
            <span class="section-label">What We Offer</span>
            <h2 class="section-title">Key Features</h2>
            <p class="text-secondary mx-auto" style="max-width:560px;">Everything you need to manage and participate in the academic process — all in one place.</p>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e8f0fe;">
                        <span class="material-icons" style="color:#1a73e8;">class</span>
                    </div>
                    <h5>Easy Class Management</h5>
                    <p>Organize classes, assign teachers and students, and keep everything structured and accessible.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e6f4ea;">
                        <span class="material-icons" style="color:#34a853;">assignment</span>
                    </div>
                    <h5>Online Assignments</h5>
                    <p>Create, distribute, and submit assignments digitally — with deadlines, file attachments, and grading.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fce8e6;">
                        <span class="material-icons" style="color:#ea4335;">bar_chart</span>
                    </div>
                    <h5>Performance Monitoring</h5>
                    <p>Track student grades and academic progress with clear, actionable performance reports.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fef7e0;">
                        <span class="material-icons" style="color:#f9ab00;">campaign</span>
                    </div>
                    <h5>Announcements & Notifications</h5>
                    <p>Post class announcements and receive real-time notifications to stay updated on academic activities.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Footer ── -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1a73e8,#34a853);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <span class="material-icons" style="color:#fff;font-size:20px;"><img src="{{ asset('logo.jpg') }}" alt="CSTA-LMS Logo" style="height:40px;width:auto;object-fit:contain;border-radius:6px;"></span>
                    </div>
                    <div>
                        <div style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#fff;font-size:14px;">CSTA-LMS</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.6);">Learning Management System</div>
                    </div>
                </div>
                <p style="font-size:13px;line-height:1.7;">
                    College De Sta. Teresa De Avila Learning Management System — empowering students, teachers, and administrators through digital education.
                </p>
            </div>
            <div class="col-md-3 offset-md-1">
                <h6>Quick Links</h6>
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#features">Features</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
            <div class="col-md-3">
                <h6>Contact Us</h6>
                <p style="font-size:13px;line-height:1.8;margin:0;">
                    <span class="material-icons align-middle me-1" style="font-size:15px;">location_on</span>
                    College De Sta. Teresa De Avila<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Philippines<br>
                    <span class="material-icons align-middle me-1" style="font-size:15px;">phone</span>
                    (000) 000-0000<br>
                    <span class="material-icons align-middle me-1" style="font-size:15px;">email</span>
                    info@csta.edu.ph
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} College De Sta. Teresa De Avila. All rights reserved.
        </div>
    </div>
</footer>

<!-- Scroll to top -->
<a href="" class="scroll-to-top">
    <span class="material-icons" style="font-size:20px;">keyboard_arrow_up</span>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
</body>
</html>

<?php
require_once __DIR__ . '/php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Your Ultimate Event Experience</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #0a0e27;
            color: #fff;
            overflow-x: hidden;
        }

        /* ===== UNIQUE LOADING SCREEN ===== */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 1s ease-out, transform 1s ease-out;
        }

        #loading-screen.fade-out {
            opacity: 0;
            transform: scale(1.1);
            pointer-events: none;
        }

        .loader-container {
            text-align: center;
            position: relative;
        }

        /* Animated Event Icon */
        .event-icon {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .event-icon i {
            font-size: 100px;
            background: linear-gradient(45deg, #fff, #ffd700, #fff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 2s linear infinite;
        }

        @keyframes shine {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        /* Circular Progress */
        .progress-ring {
            width: 200px;
            height: 200px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .progress-ring circle {
            fill: none;
            stroke: rgba(255, 255, 255, 0.3);
            stroke-width: 4;
        }

        .progress-ring .progress {
            stroke: #fff;
            stroke-linecap: round;
            stroke-dasharray: 565.48;
            stroke-dashoffset: 565.48;
            animation: progress 2s ease-out infinite;
        }

        @keyframes progress {
            to { stroke-dashoffset: 0; }
        }

        .loading-text {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #fff, #ffd700);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s ease-in-out infinite;
        }

        .loading-subtitle {
            font-size: 16px;
            opacity: 0.9;
            letter-spacing: 3px;
            animation: fadeInOut 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* Particle Background */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 20px;
            height: 20px;
            background: radial-gradient(circle, rgba(255,255,255,0.8), transparent);
            border-radius: 50%;
            animation: particleMove 20s linear infinite;
        }

        @keyframes particleMove {
            0% {
                transform: translateY(100vh) translateX(0) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(100px) scale(1);
                opacity: 0;
            }
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            opacity: 0;
            animation: fadeIn 1s ease-out 0.5s forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 50%, #2a2f4a 100%);
            z-index: -1;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(240, 147, 251, 0.2) 0%, transparent 50%);
            animation: moveGradient 15s ease infinite;
        }

        @keyframes moveGradient {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-50px, -50px); }
        }

        /* Floating Shapes */
        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            border: 2px solid #667eea;
            border-radius: 50%;
            top: 10%;
            right: 10%;
            animation: rotate 20s linear infinite;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            border: 2px solid #764ba2;
            border-radius: 30%;
            bottom: 20%;
            left: 5%;
            animation: rotate 15s linear infinite reverse;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 20%;
            top: 50%;
            left: 50%;
            animation: floatShape 10s ease-in-out infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-50px, -50px); }
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 5px 30px rgba(102, 126, 234, 0.3);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            padding: 10px 0;
            background: rgba(10, 14, 39, 0.95);
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: #fff !important;
            margin: 0 15px;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 0 50px;
            position: relative;
        }

        .hero-content {
            text-align: center;
            z-index: 1;
        }

        .hero-title {
            font-size: 72px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff, #667eea, #764ba2, #f093fb);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: titleGlow 3s ease-in-out infinite;
        }

        @keyframes titleGlow {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.3); }
        }

        .hero-subtitle {
            font-size: 24px;
            margin-bottom: 40px;
            opacity: 0.9;
            animation: slideUp 1s ease-out 0.5s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 0.9;
                transform: translateY(0);
            }
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideUp 1s ease-out 0.7s both;
        }

        .btn-glow {
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #fff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-glow:hover::before {
            left: 100%;
        }

        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
            color: #fff;
        }

        .btn-outline-glow {
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            border: 2px solid #667eea;
            background: transparent;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-glow:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            color: #fff;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            position: relative;
            z-index: 1;
        }

        .section-title {
            text-align: center;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 60px;
            background: linear-gradient(45deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
            border-color: rgba(102, 126, 234, 0.5);
        }

        .feature-icon {
            font-size: 64px;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(5deg);
        }

        .feature-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .feature-description {
            opacity: 0.8;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .stat-number {
            font-size: 56px;
            font-weight: 800;
            background: linear-gradient(45deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 18px;
            opacity: 0.8;
        }

        /* Footer */
        .footer {
            padding: 40px 0;
            text-align: center;
            background: rgba(10, 14, 39, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 48px;
            }
            .hero-subtitle {
                font-size: 18px;
            }
            .section-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="particles">
            <?php for($i = 0; $i < 20; $i++): ?>
                <div class="particle" style="left: <?php echo rand(0, 100); ?>%; animation-delay: <?php echo rand(0, 10); ?>s;"></div>
            <?php endfor; ?>
        </div>
        <div class="loader-container">
            <svg class="progress-ring" width="200" height="200">
                <circle cx="100" cy="100" r="90"></circle>
                <circle class="progress" cx="100" cy="100" r="90"></circle>
            </svg>
            <div class="event-icon">
                <i class="fas fa-calendar-star"></i>
            </div>
            <h2 class="loading-text">EventHub</h2>
            <p class="loading-subtitle">LOADING EXPERIENCE</p>
        </div>
    </div>

    <!-- Animated Background -->
    <div class="animated-bg"></div>
    
    <!-- Floating Shapes -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-calendar-star"></i> EventHub
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: rgba(255,255,255,0.1);">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="events-calendar.php">Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="php/calendar-view.php">Calendar</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">
                                    <i class="fas fa-user-circle"></i> Profile
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="admin/manage-events.php">Admin</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="php/logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section" id="home">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">Welcome to EventHub</h1>
                    <p class="hero-subtitle">Your Ultimate Student Event Management Experience</p>
                    <div class="cta-buttons">
                            <?php if (isLoggedIn()): ?>
                            <a href="events-calendar.php" class="btn-glow">
                                <i class="fas fa-calendar-alt"></i> Browse Events
                            </a>
                            <a href="profile.php" class="btn-outline-glow">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <?php else: ?>
                            <a href="register.php" class="btn-glow">
                                <i class="fas fa-rocket"></i> Get Started
                            </a>
                            <a href="login.php" class="btn-outline-glow">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Why Choose EventHub?</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h3 class="feature-title">Smart Calendar</h3>
                            <p class="feature-description">
                                View all upcoming events in an interactive, easy-to-use calendar interface with real-time updates.
                            </p>
                            <a href="php/calendar-view.php" class="btn btn-sm btn-outline-glow mt-3">
                                Explore Calendar
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3 class="feature-title">Instant Registration</h3>
                            <p class="feature-description">
                                Register for events with just one click. Quick, easy, and secure event participation.
                            </p>
                            <a href="events-calendar.php" class="btn btn-sm btn-outline-glow mt-3">
                                View Events
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h3 class="feature-title">Event Management</h3>
                            <p class="feature-description">
                                Powerful admin tools to create, manage, and track campus events efficiently.
                            </p>
                            <?php if (isAdmin()): ?>
                                <a href="admin/manage-events.php" class="btn btn-sm btn-outline-glow mt-3">
                                    Manage Events
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-sm btn-outline-glow mt-3">
                                    Admin Access
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <?php if ($db): ?>
            <section class="stats-section">
                <div class="container">
                    <div class="row">
                        <?php
                        try {
                            $total_events = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
                            $total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
                            $total_registrations = $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
                        } catch (Exception $e) {
                            $total_events = 0;
                            $total_users = 0;
                            $total_registrations = 0;
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $total_events; ?>+</div>
                                <div class="stat-label">Total Events</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $total_users; ?>+</div>
                                <div class="stat-label">Active Students</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $total_registrations; ?>+</div>
                                <div class="stat-label">Registrations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <p>&copy; 2025 EventHub. All rights reserved.</p>
                <p style="opacity: 0.6; font-size: 14px;">Your Ultimate Student Event Management System</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('fade-out');
                setTimeout(function() {
                    document.getElementById('loading-screen').style.display = 'none';
                }, 1000);
            }, 2000); // Show loading for 2 seconds
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax effect for floating shapes
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 50;
                const xMove = (x - 0.5) * speed;
                const yMove = (y - 0.5) * speed;
                shape.style.transform = `translate(${xMove}px, ${yMove}px)`;
            });
        });
    </script>
</body>
</html>

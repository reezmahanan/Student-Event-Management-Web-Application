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
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- All main styles are now in assets/css/style.css -->
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
                <div class="row row-equal-height">
                    <div class="col-md-4 d-flex">
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
                    <div class="col-md-4 d-flex">
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
                    <div class="col-md-4 d-flex">
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

<?php
require_once __DIR__ . '/php/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Check if database is connected
if (!$db) {
    die("Database connection failed. Please check your configuration.");
}

$user_id = $_SESSION['user_id'];

// Get user details
try {
    $user_query = "SELECT * FROM users WHERE user_id = :user_id";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->bindParam(":user_id", $user_id);
    $user_stmt->execute();
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    die("Error fetching user details: " . $e->getMessage());
}

// Get user's event registrations
try {
    $registrations_query = "SELECT e.*, r.registration_date 
                       FROM events e 
                       JOIN registrations r ON e.event_id = r.event_id 
                       WHERE r.user_id = :user_id 
                       ORDER BY r.registration_date DESC";
    $registrations_stmt = $db->prepare($registrations_query);
    $registrations_stmt->bindParam(":user_id", $user_id);
    $registrations_stmt->execute();
    $registrations = $registrations_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $registrations = [];
}

// Get user's feedback
try {
    $feedback_query = "SELECT ef.*, e.title 
                  FROM event_feedback ef 
                  JOIN events e ON ef.event_id = e.event_id 
                  WHERE ef.user_id = :user_id 
                  ORDER BY ef.created_at DESC";
    $feedback_stmt = $db->prepare($feedback_query);
    $feedback_stmt->bindParam(":user_id", $user_id);
    $feedback_stmt->execute();
    $user_feedback = $feedback_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user_feedback = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding-top: 70px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .profile-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 40px 0; }
        .profile-card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: none; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; margin: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .nav-pills .nav-link.active { background: linear-gradient(135deg, #667eea, #764ba2); }
        .event-card { transition: transform 0.3s; }
        .event-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calendar-alt"></i> EventHub
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="events-calendar.php">Events</a>
                <a class="nav-link active" href="profile.php">Profile</a>
                <?php if (isAdmin()): ?>
                    <a class="nav-link" href="admin/manage-events.php">Admin</a>
                <?php endif; ?>
                <a class="nav-link" href="php/logout.php">Logout (<?php echo htmlspecialchars($user['name']); ?>)</a>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                </div>
                <div class="col-md-10">
                    <h1 class="display-5"><?php echo htmlspecialchars($user['name']); ?></h1>
                    <p class="lead mb-0">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                        <?php if ($user['contact_number']): ?>
                            <span class="ms-3"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($user['contact_number']); ?></span>
                        <?php endif; ?>
                    </p>
                    <p class="mb-0">
                        <span class="badge bg-<?php echo ($user['user_type'] == 'admin') ? 'danger' : 'success'; ?>">
                            <?php echo ucfirst($user['user_type']); ?>
                        </span>
                        <?php if ($user['student_id']): ?>
                            <span class="badge bg-info ms-2">ID: <?php echo htmlspecialchars($user['student_id']); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="text-primary"><?php echo count($registrations); ?></h3>
                    <p>Events Registered</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="text-success"><?php echo count($user_feedback); ?></h3>
                    <p>Reviews Given</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="text-info">
                        <?php 
                        $avg_rating = 0;
                        if (!empty($user_feedback)) {
                            $total_rating = 0;
                            foreach ($user_feedback as $feedback) {
                                $total_rating += $feedback['rating'];
                            }
                            $avg_rating = round($total_rating / count($user_feedback), 1);
                        }
                        echo $avg_rating;
                        ?>
                    </h3>
                    <p>Average Rating</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="text-warning">
                        <?php echo date('M Y', strtotime($user['created_at'])); ?>
                    </h3>
                    <p>Member Since</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <!-- Navigation -->
                <div class="card profile-card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#registrations" data-bs-toggle="tab">
                                    <i class="fas fa-calendar-check me-2"></i>My Events
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#feedback" data-bs-toggle="tab">
                                    <i class="fas fa-comments me-2"></i>My Reviews
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#settings" data-bs-toggle="tab">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content">
                    <!-- My Events Tab -->
                    <div class="tab-pane fade show active" id="registrations">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>My Registered Events</h4>
                            </div>
                            <div class="card-body">
                                <?php if (empty($registrations)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5>No Event Registrations</h5>
                                        <p class="text-muted">You haven't registered for any events yet.</p>
                                        <a href="events.php" class="btn btn-primary">Browse Events</a>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($registrations as $event): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card event-card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-primary"><?php echo $event['category'] ?? 'General'; ?></span>
                                                        <small class="text-muted">
                                                            Registered: <?php echo date('M j', strtotime($event['registration_date'])); ?>
                                                        </small>
                                                    </div>
                                                    <h6 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                    <p class="card-text small text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('M j, Y g:i A', strtotime($event['event_date'])); ?>
                                                    </p>
                                                    <p class="card-text small text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        <?php echo htmlspecialchars($event['venue']); ?>
                                                    </p>
                                                    <div class="mt-2">
                                                        <a href="event-details.php?id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-outline-primary">View Event</a>
                                                        <a href="event-feedback.php?id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-outline-success">Give Feedback</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- My Reviews Tab -->
                    <div class="tab-pane fade" id="feedback">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-comments me-2"></i>My Reviews</h4>
                            </div>
                            <div class="card-body">
                                <?php if (empty($user_feedback)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                        <h5>No Reviews Given</h5>
                                        <p class="text-muted">You haven't reviewed any events yet.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($user_feedback as $feedback): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="text-primary"><?php echo htmlspecialchars($feedback['title']); ?></h6>
                                            <small class="text-muted"><?php echo date('M j, Y', strtotime($feedback['created_at'])); ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $feedback['rating']): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-warning"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            <span class="ms-2 fw-bold"><?php echo $feedback['rating']; ?>/5</span>
                                        </div>
                                        <p class="mb-0"><?php echo htmlspecialchars($feedback['comments']); ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-cog me-2"></i>Account Settings</h4>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Student ID</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['student_id'] ?? 'Not provided'); ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['contact_number'] ?? 'Not provided'); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Contact administrator to update your personal information.
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tab functionality
        var triggerTabList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="tab"]'));
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    </script>
</body>
</html>
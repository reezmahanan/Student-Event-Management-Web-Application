<?php
require_once __DIR__ . '/../php/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    redirect('../db_structure.sql/login.php');
}

if (!isAdmin()) {
    redirect('../index.php');
}

// Get statistics
$stats = [];
try {
    // Total users
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Total students
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE user_type = 'student'");
    $stats['total_students'] = $stmt->fetchColumn();
    
    // Total admins
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'");
    $stats['total_admins'] = $stmt->fetchColumn();
    
    // Total events
    $stmt = $db->query("SELECT COUNT(*) FROM events");
    $stats['total_events'] = $stmt->fetchColumn();
    
    // Upcoming events
    $stmt = $db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()");
    $stats['upcoming_events'] = $stmt->fetchColumn();
    
    // Total registrations
    $stmt = $db->query("SELECT COUNT(*) FROM registrations");
    $stats['total_registrations'] = $stmt->fetchColumn();
    
    // Total feedback
    $stmt = $db->query("SELECT COUNT(*) FROM feedback");
    $stats['total_feedback'] = $stmt->fetchColumn();
    
    // Total volunteers
    $stmt = $db->query("SELECT COUNT(*) FROM volunteers");
    $stats['total_volunteers'] = $stmt->fetchColumn();
    
    // Pending volunteers
    $stmt = $db->query("SELECT COUNT(*) FROM volunteers WHERE status = 'pending'");
    $stats['pending_volunteers'] = $stmt->fetchColumn();
    
    // Total notifications
    $stmt = $db->query("SELECT COUNT(*) FROM notifications");
    $stats['total_notifications'] = $stmt->fetchColumn();
    
    // Recent users
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent events
    $stmt = $db->query("SELECT e.*, u.name as creator_name FROM events e 
                        LEFT JOIN users u ON e.created_by = u.user_id 
                        ORDER BY e.created_at DESC LIMIT 5");
    $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Events by category (if event_categories table exists)
    try {
        $stmt = $db->query("SELECT category_name, COUNT(*) as count FROM event_categories ec 
                           LEFT JOIN events e ON e.title LIKE CONCAT('%', ec.category_name, '%') 
                           GROUP BY ec.category_id");
        $events_by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $events_by_category = [];
    }
    
} catch (Exception $e) {
    $stats = [
        'total_users' => 0,
        'total_students' => 0,
        'total_admins' => 0,
        'total_events' => 0,
        'upcoming_events' => 0,
        'total_registrations' => 0,
        'total_feedback' => 0,
        'total_volunteers' => 0,
        'pending_volunteers' => 0,
        'total_notifications' => 0
    ];
    $recent_users = [];
    $recent_events = [];
    $events_by_category = [];
}

$user_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-icon {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .main-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .quick-action-btn {
            margin: 5px;
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .badge-admin { background: #dc3545; }
        .badge-student { background: #667eea; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="main-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-tachometer-alt text-primary"></i> Admin Dashboard</h1>
                    <p class="mb-0 text-muted">Welcome back, <strong><?php echo htmlspecialchars($user_name); ?></strong>!</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="create-event.php" class="btn btn-success quick-action-btn">
                        <i class="fas fa-plus"></i> New Event
                    </a>
                    <a href="manage-events.php" class="btn btn-primary quick-action-btn">
                        <i class="fas fa-calendar"></i> Manage Events
                    </a>
                    <a href="manage-users.php" class="btn btn-info quick-action-btn">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="../php/logout.php" class="btn btn-danger quick-action-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="fas fa-users stat-icon text-primary"></i>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number text-primary"><?php echo $stats['total_users']; ?></div>
                    <small class="text-muted">
                        <i class="fas fa-user-graduate"></i> <?php echo $stats['total_students']; ?> Students | 
                        <i class="fas fa-user-shield"></i> <?php echo $stats['total_admins']; ?> Admins
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="fas fa-calendar-alt stat-icon text-success"></i>
                    <div class="stat-label">Total Events</div>
                    <div class="stat-number text-success"><?php echo $stats['total_events']; ?></div>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> <?php echo $stats['upcoming_events']; ?> Upcoming
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="fas fa-clipboard-check stat-icon text-info"></i>
                    <div class="stat-label">Registrations</div>
                    <div class="stat-number text-info"><?php echo $stats['total_registrations']; ?></div>
                    <small class="text-muted">
                        <i class="fas fa-chart-line"></i> All time
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="fas fa-star stat-icon text-warning"></i>
                    <div class="stat-label">Feedback</div>
                    <div class="stat-number text-warning"><?php echo $stats['total_feedback']; ?></div>
                    <small class="text-muted">
                        <i class="fas fa-comments"></i> Reviews
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Users -->
            <div class="col-md-6">
                <div class="table-card">
                    <h4 class="mb-4"><i class="fas fa-user-plus text-primary"></i> Recent Users</h4>
                    <?php if (empty($recent_users)): ?>
                        <p class="text-muted">No users yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['user_type'] === 'admin'): ?>
                                                <span class="badge badge-admin">Admin</span>
                                            <?php else: ?>
                                                <span class="badge badge-student">Student</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="col-md-6">
                <div class="table-card">
                    <h4 class="mb-4"><i class="fas fa-calendar-plus text-success"></i> Recent Events</h4>
                    <?php if (empty($recent_events)): ?>
                        <p class="text-muted">No events yet. <a href="create-event.php">Create your first event</a>!</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Venue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_events as $event): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                                            <small class="text-muted">by <?php echo htmlspecialchars($event['creator_name'] ?? 'Unknown'); ?></small>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                        <td>
                                            <a href="edit-event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="table-card">
            <h4 class="mb-4"><i class="fas fa-link text-info"></i> Quick Links</h4>
            <div class="row">
                <div class="col-md-3">
                    <a href="create-event.php" class="btn btn-outline-success w-100 mb-3">
                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                        Create Event
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="manage-events.php" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
                        Manage Events
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="manage-users.php" class="btn btn-outline-info w-100 mb-3">
                        <i class="fas fa-users-cog fa-2x d-block mb-2"></i>
                        Manage Users
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="reports.php" class="btn btn-outline-warning w-100 mb-3">
                        <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                        View Reports
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="manage-volunteers.php" class="btn btn-outline-purple w-100 mb-3">
                        <i class="fas fa-hands-helping fa-2x d-block mb-2"></i>
                        Volunteers
                        <?php if ($stats['pending_volunteers'] > 0): ?>
                            <span class="badge bg-danger"><?php echo $stats['pending_volunteers']; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="manage-notifications.php" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="fas fa-bell fa-2x d-block mb-2"></i>
                        Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

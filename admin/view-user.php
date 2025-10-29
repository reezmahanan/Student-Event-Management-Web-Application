<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

// Get user ID
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    die('Invalid user ID');
}

// Fetch user details
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user = false;
}

if (!$user) {
    die('User not found');
}

// Get user's event registrations
try {
    $stmt = $db->prepare("
        SELECT r.*, e.title, e.event_date, e.event_time, e.venue, e.max_participants
        FROM registrations r
        LEFT JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = :user_id
        ORDER BY e.event_date DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $registrations = [];
}

// Get user's feedback
try {
    $stmt = $db->prepare("
        SELECT f.*, e.title as event_title
        FROM feedback f
        LEFT JOIN events e ON f.event_id = e.event_id
        WHERE f.user_id = :user_id
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $feedback = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .user-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #667eea;
            margin: 0 auto 15px;
        }
        .info-row {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .badge-type {
            font-size: 0.9rem;
            padding: 5px 15px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="manage-events.php">Manage Events</a>
            <a class="nav-link" href="manage-users.php">Manage Users</a>
            <a class="nav-link" href="create-event.php">Create Event</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="user-header">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="text-center mb-2"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="text-center mb-0">
                <span class="badge badge-type <?php echo $user['user_type'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                    <?php echo strtoupper($user['user_type'] ?? 'Student'); ?>
                </span>
            </p>
        </div>
        
        <div class="card-body">
            <h4 class="mb-3"><i class="fas fa-info-circle text-primary"></i> User Information</h4>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-id-badge"></i> User ID:</strong></div>
                    <div class="col-md-8"><?php echo $user['user_id']; ?></div>
                </div>
            </div>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-id-card"></i> Student ID:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($user['student_id'] ?? 'N/A'); ?></div>
                </div>
            </div>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-envelope"></i> Email:</strong></div>
                    <div class="col-md-8"><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></a></div>
                </div>
            </div>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-phone"></i> Contact:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($user['contact_number'] ?? 'N/A'); ?></div>
                </div>
            </div>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-calendar"></i> Joined:</strong></div>
                    <div class="col-md-8"><?php echo date('F d, Y', strtotime($user['created_at'] ?? 'now')); ?></div>
                </div>
            </div>
            
            <div class="info-row">
                <div class="row">
                    <div class="col-md-4"><strong><i class="fas fa-clock"></i> Last Updated:</strong></div>
                    <div class="col-md-8"><?php echo date('F d, Y g:i A', strtotime($user['updated_at'] ?? $user['created_at'] ?? 'now')); ?></div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="edit-user.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                <a href="manage-users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
                <a href="delete-user.php?id=<?php echo $user_id; ?>" class="btn btn-danger float-end" onclick="return confirm('Are you sure you want to delete this user?');">
                    <i class="fas fa-trash"></i> Delete User
                </a>
            </div>
        </div>
    </div>

    <!-- Event Registrations -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-calendar-check"></i> Event Registrations (<?php echo count($registrations); ?>)</h4>
        </div>
        <div class="card-body">
            <?php if (empty($registrations)): ?>
                <p class="text-muted">No event registrations yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Registered On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($reg['title'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($reg['event_date'] ?? 'now')); ?></td>
                                <td><?php echo htmlspecialchars($reg['venue'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($reg['status'] ?? 'confirmed') {
                                            'confirmed' => 'success',
                                            'waitlist' => 'warning',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($reg['status'] ?? 'confirmed'); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($reg['registration_date'] ?? 'now')); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Feedback -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-star"></i> Feedback Submitted (<?php echo count($feedback); ?>)</h4>
        </div>
        <div class="card-body">
            <?php if (empty($feedback)): ?>
                <p class="text-muted">No feedback submitted yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedback as $fb): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fb['event_title'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    $rating = $fb['rating'] ?? 0;
                                    for ($i = 0; $i < 5; $i++) {
                                        echo $i < $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($fb['comment'] ?? '', 0, 100)); ?></td>
                                <td><?php echo date('M d, Y', strtotime($fb['created_at'] ?? 'now')); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if ($user_id <= 0) {
    die('Invalid user ID');
}

// Fetch user details for confirmation
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

// Prevent deleting yourself
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
    die('<script>alert("You cannot delete your own account!"); window.location.href="manage-users.php";</script>');
}

$error_message = '';
$success = false;

// Process deletion if confirmed
if ($confirmed) {
    try {
        $db->beginTransaction();
        
        // Delete related records first (cascade)
        // Delete user's registrations
        $stmt = $db->prepare("DELETE FROM registrations WHERE user_id = :id");
        $stmt->execute([':id' => $user_id]);
        
        // Delete user's feedback
        $stmt = $db->prepare("DELETE FROM feedback WHERE user_id = :id");
        $stmt->execute([':id' => $user_id]);
        
        // Delete user's volunteer applications (if exists)
        try {
            $stmt = $db->prepare("DELETE FROM volunteers WHERE user_id = :id");
            $stmt->execute([':id' => $user_id]);
        } catch (Exception $e) {
            // Table might not exist
        }
        
        // Delete user's notifications (if exists)
        try {
            $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = :id");
            $stmt->execute([':id' => $user_id]);
        } catch (Exception $e) {
            // Table might not exist
        }
        
        // Finally, delete the user
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $user_id]);
        
        $db->commit();
        $success = true;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error_message = 'Failed to delete user: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete User - Admin</title>
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .danger-zone {
            background: #f8d7da;
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="manage-users.php">Manage Users</a>
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Delete User</h4>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>User deleted successfully!</strong>
                    <p class="mb-0">The user and all related records have been permanently removed.</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'manage-users.php';
                    }, 2000);
                </script>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
                <a href="manage-users.php" class="btn btn-secondary">Back to Users</a>
            <?php else: ?>
                <div class="warning-box">
                    <h5><i class="fas fa-exclamation-triangle text-warning"></i> Warning!</h5>
                    <p>You are about to delete the following user:</p>
                    <ul>
                        <li><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($user['student_id'] ?? 'N/A'); ?></li>
                        <li><strong>User Type:</strong> <?php echo htmlspecialchars($user['user_type'] ?? 'student'); ?></li>
                        <li><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></li>
                    </ul>
                </div>

                <div class="danger-zone">
                    <h5><i class="fas fa-skull-crossbones text-danger"></i> Danger Zone</h5>
                    <p><strong>This action cannot be undone!</strong></p>
                    <p>Deleting this user will also permanently delete:</p>
                    <ul>
                        <li><i class="fas fa-calendar-times"></i> All event registrations</li>
                        <li><i class="fas fa-comment-slash"></i> All feedback and ratings</li>
                        <li><i class="fas fa-hand-paper"></i> All volunteer applications</li>
                        <li><i class="fas fa-bell-slash"></i> All notifications</li>
                    </ul>
                    <p class="mb-0 text-danger"><strong>Are you absolutely sure you want to proceed?</strong></p>
                </div>

                <div class="d-flex gap-2 justify-content-between">
                    <a href="manage-users.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <div>
                        <a href="view-user.php?id=<?php echo $user_id; ?>" class="btn btn-info btn-lg me-2">
                            <i class="fas fa-eye"></i> View User Details
                        </a>
                        <a href="delete-user.php?id=<?php echo $user_id; ?>&confirm=yes" 
                           class="btn btn-danger btn-lg"
                           onclick="return confirm('FINAL CONFIRMATION: Delete user <?php echo htmlspecialchars($user['name']); ?>? This CANNOT be undone!');">
                            <i class="fas fa-trash-alt"></i> Yes, Delete Permanently
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

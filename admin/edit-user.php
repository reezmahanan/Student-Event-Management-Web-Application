<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

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

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $user_type = trim($_POST['user_type'] ?? 'student');
    $new_password = trim($_POST['new_password'] ?? '');
    
    if ($name === '' || $email === '') {
        $error_message = 'Name and email are required fields.';
    } else {
        try {
            // Check if email already exists for another user
            $stmt = $db->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :id");
            $stmt->execute([':email' => $email, ':id' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $error_message = 'Email already exists for another user.';
            } else {
                // Update user details
                $stmt = $db->prepare("
                    UPDATE users 
                    SET name = :name, 
                        email = :email, 
                        student_id = :student_id, 
                        contact_number = :contact_number, 
                        user_type = :user_type,
                        updated_at = NOW()
                    WHERE user_id = :id
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':student_id' => $student_id,
                    ':contact_number' => $contact_number,
                    ':user_type' => $user_type,
                    ':id' => $user_id
                ]);
                
                // Update password if provided
                if ($new_password !== '') {
                    if (strlen($new_password) < 6) {
                        $error_message = 'Password must be at least 6 characters long.';
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE users SET password = :password WHERE user_id = :id");
                        $stmt->execute([':password' => $hashed_password, ':id' => $user_id]);
                    }
                }
                
                if ($error_message === '') {
                    $success_message = 'User updated successfully!';
                    // Refresh user data
                    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :id");
                    $stmt->execute([':id' => $user_id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
        } catch (Exception $e) {
            $error_message = 'Failed to update user: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User - Admin</title>
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
            margin-bottom: 20px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit User</h4>
        </div>
        <div class="card-body">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-envelope"></i> Email *</label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-id-card"></i> Student ID</label>
                        <input type="text" name="student_id" class="form-control" 
                               value="<?php echo htmlspecialchars($user['student_id'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-phone"></i> Contact Number</label>
                        <input type="tel" name="contact_number" class="form-control" 
                               value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-user-tag"></i> User Type *</label>
                        <select name="user_type" class="form-select" required>
                            <option value="student" <?php echo ($user['user_type'] ?? 'student') === 'student' ? 'selected' : ''; ?>>Student</option>
                            <option value="admin" <?php echo ($user['user_type'] ?? 'student') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <small class="text-muted">Choose carefully - admins have full access</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-lock"></i> New Password (optional)</label>
                        <input type="password" name="new_password" class="form-control" 
                               placeholder="Leave blank to keep current password">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                            <p class="mb-1"><strong>Created:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                            <p class="mb-0"><strong>Last Updated:</strong> <?php echo date('M d, Y g:i A', strtotime($user['updated_at'] ?? $user['created_at'] ?? 'now')); ?></p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex gap-2 justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="view-user.php?id=<?php echo $user_id; ?>" class="btn btn-info btn-lg">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="manage-users.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <a href="delete-user.php?id=<?php echo $user_id; ?>" class="btn btn-danger btn-lg" 
                       onclick="return confirm('Are you sure you want to delete this user?');">
                        <i class="fas fa-trash"></i> Delete User
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const newPassword = document.querySelector('input[name="new_password"]').value;
        if (newPassword !== '' && newPassword.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long');
            return false;
        }
    });
</script>
</body>
</html>

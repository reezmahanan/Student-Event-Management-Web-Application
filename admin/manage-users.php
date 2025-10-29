<?php
require_once __DIR__ . '/../php/config.php';

// Admin access
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

$users = [];
try {
    $stmt = $db->query("SELECT user_id, name, email, student_id, contact_number, user_type, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="manage-events.php">Manage Events</a>
            <a class="nav-link active" href="manage-users.php">Manage Users</a>
            <a class="nav-link" href="create-event.php">Create Event</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Manage Users</h4>
        </div>
        <div class="card-body">
            <a href="../register.php" class="btn btn-success mb-3"><i class="fas fa-user-plus"></i> Create User</a>

            <?php if (empty($users)): ?>
                <div class="alert alert-info">No users found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Student ID</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['student_id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($u['contact_number'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($u['user_type'] ?? 'student'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($u['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <a href="view-user.php?id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="delete-user.php?id=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');"><i class="fas fa-trash"></i></a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

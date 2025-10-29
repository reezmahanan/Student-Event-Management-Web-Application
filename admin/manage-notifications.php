<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

// Fetch all notifications
try {
    $stmt = $db->query("
        SELECT n.*, u.name as user_name, u.email
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.user_id
        ORDER BY n.created_at DESC
    ");
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $notifications = [];
}

// Get statistics
$total_notifications = count($notifications);
$unread = count(array_filter($notifications, function($n) { return !($n['is_read'] ?? false); }));
$read = count(array_filter($notifications, function($n) { return ($n['is_read'] ?? false); }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Notifications - Admin</title>
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
        .stat-box {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="manage-events.php">Events</a>
            <a class="nav-link" href="manage-users.php">Users</a>
            <a class="nav-link active" href="manage-notifications.php">Notifications</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-bell"></i> Manage Notifications</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-bell fa-2x text-primary"></i>
                        <div class="stat-number text-primary"><?php echo $total_notifications; ?></div>
                        <div class="text-muted">Total Notifications</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-envelope fa-2x text-warning"></i>
                        <div class="stat-number text-warning"><?php echo $unread; ?></div>
                        <div class="text-muted">Unread</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-envelope-open fa-2x text-success"></i>
                        <div class="stat-number text-success"><?php echo $read; ?></div>
                        <div class="text-muted">Read</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button class="btn btn-success" onclick="showSendNotificationModal()">
                    <i class="fas fa-paper-plane"></i> Send New Notification
                </button>
                <button class="btn btn-primary" onclick="sendBulkNotification()">
                    <i class="fas fa-bullhorn"></i> Send to All Users
                </button>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No notifications sent yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Sent On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notif): ?>
                            <tr class="<?php echo ($notif['is_read'] ?? false) ? '' : 'table-warning'; ?>">
                                <td><?php echo $notif['notification_id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($notif['user_name'] ?? 'N/A'); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($notif['email'] ?? ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(substr($notif['message'] ?? '', 0, 100)); ?>...</td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($notif['type'] ?? 'general'); ?></span></td>
                                <td>
                                    <?php if ($notif['is_read'] ?? false): ?>
                                        <span class="badge bg-success">Read</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Unread</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y g:i A', strtotime($notif['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteNotification(<?php echo $notif['notification_id']; ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Send Notification Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane"></i> Send Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendNotificationForm">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select User</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Select User --</option>
                            <?php
                            try {
                                $stmt = $db->query("SELECT user_id, name, email, user_type FROM users ORDER BY name");
                                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($users as $u) {
                                    echo '<option value="' . $u['user_id'] . '">' . htmlspecialchars($u['name']) . ' (' . htmlspecialchars($u['email']) . ') - ' . ucfirst($u['user_type']) . '</option>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="">Error loading users</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Notification Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="general">General</option>
                            <option value="success">Success</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Enter notification message..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="sendNotification()">
                    <i class="fas fa-paper-plane"></i> Send Notification
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send Bulk Notification Modal -->
<div class="modal fade" id="sendBulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-bullhorn"></i> Send to All Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendBulkForm">
                    <div class="mb-3">
                        <label for="user_type_bulk" class="form-label">Target Users</label>
                        <select class="form-select" id="user_type_bulk" name="user_type" required>
                            <option value="all">All Users</option>
                            <option value="student">Students Only</option>
                            <option value="organizer">Organizers Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type_bulk" class="form-label">Notification Type</label>
                        <select class="form-select" id="type_bulk" name="type" required>
                            <option value="general">General</option>
                            <option value="success">Success</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message_bulk" class="form-label">Message</label>
                        <textarea class="form-control" id="message_bulk" name="message" rows="4" required placeholder="Enter notification message for all users..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendBulkNotificationConfirm()">
                    <i class="fas fa-bullhorn"></i> Send to All
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showSendNotificationModal() {
    const modal = new bootstrap.Modal(document.getElementById('sendNotificationModal'));
    modal.show();
}

function sendNotification() {
    const form = document.getElementById('sendNotificationForm');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    fetch('send-notification.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('sendNotificationModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error.message);
    });
}

function sendBulkNotification() {
    const modal = new bootstrap.Modal(document.getElementById('sendBulkModal'));
    modal.show();
}

function sendBulkNotificationConfirm() {
    const form = document.getElementById('sendBulkForm');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const userType = document.getElementById('user_type_bulk').value;
    const message = document.getElementById('message_bulk').value;
    
    if (!confirm(`Are you sure you want to send this notification to ${userType === 'all' ? 'all users' : userType + 's'}?\n\nMessage: ${message}`)) {
        return;
    }
    
    fetch('send-notification-bulk.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('sendBulkModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error.message);
    });
}

function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('notification_id', notificationId);
    
    fetch('delete-notification.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error.message);
    });
}
</script>
</body>
</html>

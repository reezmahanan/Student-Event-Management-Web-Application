<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

// Fetch all volunteers
try {
    $stmt = $db->query("
        SELECT v.*, u.name, u.email, u.student_id, u.contact_number, e.title as event_title, e.event_date
        FROM volunteers v
        LEFT JOIN users u ON v.user_id = u.user_id
        LEFT JOIN events e ON v.event_id = e.event_id
        ORDER BY v.created_at DESC
    ");
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $volunteers = [];
}

// Get statistics
$total_volunteers = count($volunteers);
$pending = count(array_filter($volunteers, function($v) { return ($v['status'] ?? 'pending') === 'pending'; }));
$approved = count(array_filter($volunteers, function($v) { return ($v['status'] ?? 'pending') === 'approved'; }));
$rejected = count(array_filter($volunteers, function($v) { return ($v['status'] ?? 'pending') === 'rejected'; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Volunteers - Admin</title>
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
            <a class="nav-link active" href="manage-volunteers.php">Volunteers</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-hands-helping"></i> Manage Volunteers</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-users fa-2x text-primary"></i>
                        <div class="stat-number text-primary"><?php echo $total_volunteers; ?></div>
                        <div class="text-muted">Total Applications</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                        <div class="stat-number text-warning"><?php echo $pending; ?></div>
                        <div class="text-muted">Pending</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                        <div class="stat-number text-success"><?php echo $approved; ?></div>
                        <div class="text-muted">Approved</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                        <div class="stat-number text-danger"><?php echo $rejected; ?></div>
                        <div class="text-muted">Rejected</div>
                    </div>
                </div>
            </div>

            <?php if (empty($volunteers)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No volunteer applications yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Volunteer Name</th>
                                <th>Event</th>
                                <th>Contact</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($volunteers as $vol): ?>
                            <tr>
                                <td><?php echo $vol['volunteer_id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($vol['name'] ?? 'N/A'); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($vol['student_id'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($vol['event_title'] ?? 'N/A'); ?><br>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($vol['event_date'] ?? 'now')); ?></small>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($vol['email'] ?? ''); ?>"><?php echo htmlspecialchars($vol['email'] ?? 'N/A'); ?></a><br>
                                    <small><?php echo htmlspecialchars($vol['contact_number'] ?? 'N/A'); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($vol['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($vol['status'] ?? 'pending') {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning'
                                        };
                                    ?>">
                                        <?php echo ucfirst($vol['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view-user.php?id=<?php echo $vol['user_id']; ?>" class="btn btn-sm btn-info" title="View User">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (($vol['status'] ?? 'pending') === 'pending'): ?>
                                        <button class="btn btn-sm btn-success" onclick="updateVolunteerStatus(<?php echo $vol['volunteer_id']; ?>, 'approved')" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="updateVolunteerStatus(<?php echo $vol['volunteer_id']; ?>, 'rejected')" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
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
<script>
function updateVolunteerStatus(volunteerId, status) {
    const action = status.charAt(0).toUpperCase() + status.slice(1);
    
    if (!confirm('Are you sure you want to ' + status + ' this volunteer application?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('volunteer_id', volunteerId);
    formData.append('status', status);
    
    fetch('update-volunteer-status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + '\n\nVolunteer: ' + data.volunteer.name);
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

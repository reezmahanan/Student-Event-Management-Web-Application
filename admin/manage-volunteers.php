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
        ORDER BY v.applied_at DESC
    ");
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $volunteers = [];
    $error_message = $e->getMessage();
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #dee2e6;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .table-hover tbody tr {
            transition: background-color 0.2s;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        #searchInput {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #dee2e6;
            transition: border-color 0.3s;
        }
        #searchInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-group .btn {
            margin: 0;
        }
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
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

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Database Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($volunteers)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No volunteer applications yet.
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search volunteers by name, email, event, or role...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="volunteersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Volunteer</th>
                                <th>Event</th>
                                <th>Role</th>
                                <th>Hours</th>
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
                                    <small class="text-muted"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($vol['student_id'] ?? 'N/A'); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($vol['event_title'] ?? 'N/A'); ?></strong><br>
                                    <small class="text-muted"><i class="fas fa-calendar"></i> <?php echo $vol['event_date'] ? date('M d, Y', strtotime($vol['event_date'])) : 'N/A'; ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-user-tag"></i> <?php echo htmlspecialchars($vol['role'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($vol['hours_committed'] ?? '0'); ?> hrs
                                    </span>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($vol['email'] ?? ''); ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($vol['email'] ?? 'N/A'); ?>
                                    </a><br>
                                    <small class="text-muted"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($vol['contact_number'] ?? 'N/A'); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $applied_date = $vol['applied_at'] ?? null;
                                    echo $applied_date ? date('M d, Y', strtotime($applied_date)) : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($vol['status'] ?? 'pending') {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning text-dark'
                                        };
                                    ?>">
                                        <?php echo ucfirst($vol['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="view-user.php?id=<?php echo $vol['user_id']; ?>" class="btn btn-sm btn-info" title="View User Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (($vol['status'] ?? 'pending') === 'pending'): ?>
                                            <button class="btn btn-sm btn-success" onclick="updateVolunteerStatus(<?php echo $vol['volunteer_id']; ?>, 'approved')" title="Approve Application">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="updateVolunteerStatus(<?php echo $vol['volunteer_id']; ?>, 'rejected')" title="Reject Application">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif ($vol['status'] === 'approved'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="updateVolunteerStatus(<?php echo $vol['volunteer_id']; ?>, 'pending')" title="Reset to Pending">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
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
    
    if (!confirm('Are you sure you want to ' + action.toLowerCase() + ' this volunteer application?')) {
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

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#volunteersTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
</body>
</html>

<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    die('Invalid event ID');
}

// Fetch event details
try {
    $stmt = $db->prepare("SELECT * FROM events WHERE event_id = :id");
    $stmt->execute([':id' => $event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $event = false;
}

if (!$event) {
    die('Event not found');
}

// Fetch all registrations for this event
try {
    $stmt = $db->prepare("
        SELECT r.*, u.name, u.email, u.student_id, u.contact_number 
        FROM registrations r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.event_id = :event_id
        ORDER BY r.registration_date DESC
    ");
    $stmt->execute([':event_id' => $event_id]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $registrations = [];
}

// Get statistics
$total_registrations = count($registrations);
$confirmed = count(array_filter($registrations, function($r) { return ($r['status'] ?? 'confirmed') === 'confirmed'; }));
$waitlist = count(array_filter($registrations, function($r) { return ($r['status'] ?? 'confirmed') === 'waitlist'; }));
$cancelled = count(array_filter($registrations, function($r) { return ($r['status'] ?? 'confirmed') === 'cancelled'; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Registrations - Admin</title>
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
        .event-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
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
            margin: 10px 0;
        }
        .badge-confirmed { background: #28a745; }
        .badge-waitlist { background: #ffc107; color: #000; }
        .badge-cancelled { background: #dc3545; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="manage-events.php">Manage Events</a>
            <a class="nav-link" href="manage-users.php">Users</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="event-header">
            <h2><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($event['title']); ?></h2>
            <p class="mb-2">
                <i class="fas fa-clock"></i> <?php echo date('F d, Y', strtotime($event['event_date'])); ?> 
                at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
            </p>
            <p class="mb-0">
                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['venue']); ?>
            </p>
        </div>
        
        <div class="card-body">
            <h4 class="mb-3"><i class="fas fa-chart-bar text-primary"></i> Registration Statistics</h4>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-users fa-2x text-primary"></i>
                        <div class="stat-number text-primary"><?php echo $total_registrations; ?></div>
                        <div class="text-muted">Total Registrations</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                        <div class="stat-number text-success"><?php echo $confirmed; ?></div>
                        <div class="text-muted">Confirmed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                        <div class="stat-number text-warning"><?php echo $waitlist; ?></div>
                        <div class="text-muted">Waitlist</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                        <div class="stat-number text-danger"><?php echo $cancelled; ?></div>
                        <div class="text-muted">Cancelled</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fas fa-list"></i> Registered Users</h4>
                <div>
                    <button class="btn btn-success" onclick="exportToCSV()">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            <?php if (empty($registrations)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No registrations for this event yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="registrationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?php echo $reg['reg_id'] ?? 'N/A'; ?></td>
                                <td><strong><?php echo htmlspecialchars($reg['name'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo htmlspecialchars($reg['student_id'] ?? 'N/A'); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($reg['email'] ?? ''); ?>"><?php echo htmlspecialchars($reg['email'] ?? 'N/A'); ?></a></td>
                                <td><?php echo htmlspecialchars($reg['contact_number'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $reg['status'] ?? 'confirmed'; ?>">
                                        <?php echo ucfirst($reg['status'] ?? 'confirmed'); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y g:i A', strtotime($reg['registration_date'] ?? 'now')); ?></td>
                                <td>
                                    <a href="view-user.php?id=<?php echo $reg['user_id'] ?? 0; ?>" class="btn btn-sm btn-info" title="View User">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="cancelRegistration(<?php echo $reg['reg_id'] ?? 0; ?>)" title="Cancel Registration">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="edit-event.php?id=<?php echo $event_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Event
                </a>
                <a href="manage-events.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function cancelRegistration(registrationId) {
    if (!confirm('Are you sure you want to cancel this registration?\n\nThis will:\n- Change status to cancelled\n- Free up a spot in the event\n- Notify the user')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('registration_id', registrationId);
    
    fetch('cancel-registration.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + '\n\nUser: ' + data.registration.user_name + '\nEvent: ' + data.registration.event_title);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Network error: ' + error.message);
    });
}

function exportToCSV() {
    const table = document.getElementById('registrationsTable');
    let csv = [];
    
    // Get headers
    const headers = Array.from(table.querySelectorAll('thead th'))
        .slice(0, -1) // Exclude Actions column
        .map(th => th.textContent.trim());
    csv.push(headers.join(','));
    
    // Get data rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = Array.from(row.querySelectorAll('td'))
            .slice(0, -1) // Exclude Actions column
            .map(td => {
                let text = td.textContent.trim();
                // Handle email links
                const link = td.querySelector('a');
                if (link) text = link.textContent.trim();
                // Escape quotes and wrap in quotes if contains comma
                if (text.includes(',')) text = '"' + text.replace(/"/g, '""') + '"';
                return text;
            });
        csv.push(cols.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'registrations_<?php echo $event_id; ?>_<?php echo date('Ymd'); ?>.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
</body>
</html>

<?php
require_once __DIR__ . '/../php/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../db_structure.sql/login.php");
    exit();
}

// Ensure a CSRF token for safe form submissions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Get current month and year from URL or default to current
$current_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$current_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Calculate previous and next month
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Attempt to read events from DB safely
try {
    $events_query = "SELECT e.*, 
                     COALESCE(SUM(eb.estimated_cost), 0) as total_budget,
                     COALESCE(SUM(eb.actual_cost), 0) as total_actual_cost
                     FROM events e
                     LEFT JOIN event_budgets eb ON e.event_id = eb.event_id
                     GROUP BY e.event_id
                     ORDER BY e.event_date ASC, e.event_time ASC";
    $events_stmt = $db->query($events_query);
    $all_events = $events_stmt ? $events_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    
    // Filter events for current month
    $month_events = array_filter($all_events, function($event) use ($current_month, $current_year) {
        $event_date = strtotime($event['event_date']);
        return (int)date('m', $event_date) === $current_month && (int)date('Y', $event_date) === $current_year;
    });
    
    // Get statistics
    $total_events = count($all_events);
    $upcoming_events = count(array_filter($all_events, function($e) {
        return strtotime($e['event_date']) >= strtotime('today');
    }));
    $month_count = count($month_events);
    
} catch (Exception $e) {
    $all_events = [];
    $month_events = [];
    $total_events = 0;
    $upcoming_events = 0;
    $month_count = 0;
}

$month_name = date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            padding-top: 70px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .calendar-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .month-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .month-title {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .stat-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .event-card {
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .event-card:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .btn-month-nav {
            min-width: 150px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> EventHub Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link active" href="manage-events.php">Manage Events</a>
                <a class="nav-link" href="create-event.php">Create Event</a>
                <a class="nav-link" href="../php/logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <!-- Calendar Header with Navigation -->
        <div class="content-card">
            <div class="calendar-header">
                <h2><i class="fas fa-calendar-alt"></i> Event Calendar Management</h2>
                <p class="mb-0">Manage all events with calendar view</p>
            </div>

            <!-- Month Navigation -->
            <div class="month-nav">
                <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" 
                   class="btn btn-outline-primary btn-month-nav">
                    <i class="fas fa-chevron-left"></i> Previous Month
                </a>
                <div class="month-title text-center">
                    <i class="fas fa-calendar"></i> <?php echo $month_name; ?>
                </div>
                <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" 
                   class="btn btn-outline-primary btn-month-nav">
                    Next Month <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <h5><i class="fas fa-calendar-check text-primary"></i> Total Events</h5>
                        <h3 class="text-primary mb-0"><?php echo $total_events; ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h5><i class="fas fa-clock text-success"></i> Upcoming</h5>
                        <h3 class="text-success mb-0"><?php echo $upcoming_events; ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h5><i class="fas fa-calendar-day text-info"></i> This Month</h5>
                        <h3 class="text-info mb-0"><?php echo $month_count; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-4">
                <a href="create-event.php" class="btn btn-success me-2">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
                <a href="?month=<?php echo date('m'); ?>&year=<?php echo date('Y'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-calendar-day"></i> Today
                </a>
            </div>
        </div>

        <!-- Events List for Current Month -->
        <div class="content-card">
            <h4 class="mb-4">
                <i class="fas fa-list"></i> Events in <?php echo $month_name; ?>
                <span class="badge bg-primary"><?php echo $month_count; ?></span>
            </h4>

            <?php if (!empty($_SESSION['flash_message'])): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($month_events)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No events scheduled for this month.
                    <a href="create-event.php" class="alert-link">Create your first event</a>
                </div>
            <?php else: ?>
                <?php foreach ($month_events as $event): 
                    $event_date = strtotime($event['event_date']);
                    $is_past = $event_date < strtotime('today');
                ?>
                <div class="event-card <?php echo $is_past ? 'opacity-75' : ''; ?>">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h3 mb-0 text-primary"><?php echo date('d', $event_date); ?></div>
                                <div class="text-muted"><?php echo date('M', $event_date); ?></div>
                                <div class="small"><?php echo date('l', $event_date); ?></div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h5 class="mb-1">
                                <?php echo htmlspecialchars($event['title']); ?>
                                <?php if (!empty($event['total_budget']) && $event['total_budget'] > 0): ?>
                                    <small class="text-muted ms-2">Budget: $<?php echo number_format((float)$event['total_budget'], 2); ?></small>
                                <?php endif; ?>
                                <?php if ($is_past): ?>
                                    <span class="badge bg-secondary">Past</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Upcoming</span>
                                <?php endif; ?>
                            </h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</p>
                            <small>
                                <i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                <i class="fas fa-map-marker-alt ms-2"></i> <?php echo htmlspecialchars($event['venue']); ?>
                            </small>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="small text-muted">Participants</div>
                            <div class="h4 text-primary mb-0">
                                <?php echo $event['current_participants'] ?? 0; ?>/<?php echo $event['max_participants']; ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="edit-event.php?id=<?php echo $event['event_id']; ?>" 
                               class="btn btn-sm btn-primary me-1" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="view-registrations.php?id=<?php echo $event['event_id']; ?>" 
                               class="btn btn-sm btn-info" title="View Registrations">
                                <i class="fas fa-users"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- All Events (Summary) -->
        <div class="content-card">
            <h4 class="mb-3"><i class="fas fa-calendar-alt"></i> All Events Overview</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Budget</th>
                            <th>Venue</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_events)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No events found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($all_events, 0, 10) as $event): 
                                $is_upcoming = strtotime($event['event_date']) >= strtotime('today');
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo (!empty($event['total_budget']) && $event['total_budget'] > 0) ? '$' . number_format((float)$event['total_budget'],2) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                <td><?php echo $event['current_participants'] ?? 0; ?>/<?php echo $event['max_participants']; ?></td>
                                <td>
                                    <?php if ($is_upcoming): ?>
                                        <span class="badge bg-success">Upcoming</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Past</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit-event.php?id=<?php echo $event['event_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

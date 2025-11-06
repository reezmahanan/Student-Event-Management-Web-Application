<?php 
// Include database connection
require_once __DIR__ . '/php/config.php';

// Get current month and year from URL parameters or use current date
$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Validate month and year
if ($current_month < 1 || $current_month > 12) {
    $current_month = date('n');
}
if ($current_year < 2020 || $current_year > 2030) {
    $current_year = date('Y');
}

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

// Get month name
$month_name = date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year));

// Cutoff date for submission (fixed project submission date: 2025-11-11)
// Events before this date are considered finalized; events on/after this date need manual review
$default_cutoff = '2025-11-11';
$cutoff = isset($_GET['cutoff']) ? $_GET['cutoff'] : $default_cutoff;
// validate cutoff basic format YYYY-MM-DD, fall back to default_cutoff
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $cutoff)) {
    $cutoff = $default_cutoff;
}

// Fetch upcoming programs (on or after cutoff) and ended programs (before cutoff)
// NOTE: previously these queries returned global results (all months). To make the page
// show results relevant to the selected month/year (so navigation works as expected),
// filter by MONTH() and YEAR() too.
$upcoming_programs = [];
$ended_programs = [];
if ($db) {
    try {
        $up_q = "SELECT * FROM events WHERE event_date >= ? AND MONTH(event_date)=? AND YEAR(event_date)=? ORDER BY event_date ASC, event_time ASC";
        $up_st = $db->prepare($up_q);
        $up_st->execute([$cutoff, $current_month, $current_year]);
        $upcoming_programs = $up_st->fetchAll(PDO::FETCH_ASSOC);

        $past_q = "SELECT * FROM events WHERE event_date < ? AND MONTH(event_date)=? AND YEAR(event_date)=? ORDER BY event_date DESC, event_time DESC";
        $past_st = $db->prepare($past_q);
        $past_st->execute([$cutoff, $current_month, $current_year]);
        $ended_programs = $past_st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // ignore and fall back to empty arrays
        $upcoming_programs = [];
        $ended_programs = [];
    }
}

// Function to get events for specific month
function getEventsForMonth($month, $year) {
    global $db;
    if (!$db) {
        return [];
    }
    try {
        $query = "SELECT event_id, title, description, event_date, event_time, venue, 
                  max_participants, current_participants, created_by, image_url, organizer 
                  FROM events 
                  WHERE MONTH(event_date) = ? AND YEAR(event_date) = ?
                  ORDER BY event_date ASC, event_time ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([$month, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getEventsForMonth: " . $e->getMessage());
        return [];
    }
}

// Today's date in site timezone
$today = date('Y-m-d');

// Get events for current month
try {
    $events = getEventsForMonth($current_month, $current_year);
} catch (Exception $e) {
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding-top: 70px; background-color: #f8f9fa; }
        .calendar-container { background: white; border-radius: 15px; padding: 30px; margin: 20px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .calendar-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .event-item { 
            border-left: 4px solid #667eea; 
            padding: 15px; 
            margin: 10px 0; 
            background: white; 
            border-radius: 8px; 
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .event-item:hover { 
            transform: translateX(5px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .event-item img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .event-date { background: #667eea; color: white; padding: 15px; border-radius: 8px; text-align: center; min-width: 80px; }
        .event-time { color: #6c757d; font-size: 0.9em; }
        .calendar-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .month-year { font-size: 1.5rem; font-weight: bold; color: #343a40; }
        .no-events { text-align: center; padding: 40px; color: #6c757d; }
        .date-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calendar-alt"></i> EventHub
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="events-calendar.php">Events</a>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="calendar-container">
            <div class="calendar-header text-center">
                <h2><i class="fas fa-calendar me-2"></i>Event Calendar</h2>
                <p class="mb-0">View all scheduled events</p>
            </div>

            <!-- Calendar Navigation -->
            <div class="calendar-nav">
                <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left"></i> Previous Month
                </a>
                <div class="month-year"><?php echo $month_name; ?></div>
                <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-outline-primary">
                    Next Month <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <!-- Removed submission-cutoff sections as requested: display scheduled programs only -->

            <!-- Events Timeline -->
            <div class="events-timeline">
                <h4 class="mb-4">Upcoming Events Timeline</h4>
            
                <?php if (empty($events)): ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times fa-3x mb-3 text-muted"></i>
                    <h5>No Events in <?php echo $month_name; ?></h5>
                    <p>There are no scheduled events for this month.</p>
                    <div class="mt-3">
                        <a href="?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-day"></i> Go to Current Month
                        </a>
                        <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-right"></i> Next Month
                        </a>
                    </div>
                </div>
                <?php else: 
                // Group events by date
                $events_by_date = [];
                foreach ($events as $event) {
                    $date = date('Y-m-d', strtotime($event['event_date']));
                    $events_by_date[$date][] = $event;
                }
                
                // Sort dates
                ksort($events_by_date);
                
                foreach ($events_by_date as $date => $date_events): 
                    $day_name = date('l', strtotime($date));
                    $formatted_date = date('F j, Y', strtotime($date));
                ?>
                <div class="date-section mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="event-date me-3">
                            <div class="fw-bold"><?php echo date('d', strtotime($date)); ?></div>
                            <div class="small"><?php echo date('M', strtotime($date)); ?></div>
                        </div>
                        <div>
                            <h5 class="mb-1"><?php echo $day_name; ?></h5>
                            <p class="text-muted mb-0"><?php echo $formatted_date; ?></p>
                        </div>
                    </div>
                    
                    <div class="events-list">
                        <?php
                        // Dedupe events for this date by title+time to avoid duplicate listings
                        $unique_events = [];
                        $seen_keys = [];
                        foreach ($date_events as $event) {
                            $key = mb_strtolower(trim($event['title'])) . '|' . date('H:i', strtotime($event['event_time']));
                            if (!in_array($key, $seen_keys, true)) {
                                $seen_keys[] = $key;
                                $unique_events[] = $event;
                            }
                        }
                        foreach ($unique_events as $event):
                            $event_date_only = date('Y-m-d', strtotime($event['event_date']));
                            $event_is_past = ($event_date_only < $today);
                        ?>
                        <div class="event-item">
                            <div class="row align-items-center">
                                <?php if (!empty($event['image_url'])): ?>
                                <div class="col-md-3">
                                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         class="img-fluid rounded"
                                         style="width: 100%; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-6">
                                <?php else: ?>
                                <div class="col-md-9">
                                <?php endif; ?>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                    <p class="mb-1 text-muted"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <div class="event-time">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                        <i class="fas fa-map-marker-alt ms-3 me-1"></i>
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                        <?php if (!empty($event['organizer'])): ?>
                                        <span class="badge bg-primary ms-2"><?php echo htmlspecialchars($event['organizer']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end"><?php if ($event_is_past): ?>
                                        <span class="text-muted small">Event Ended</span>
                                    <?php else: ?>
                                        <?php if (isLoggedIn()): ?>
                                            <form method="POST" action="php/events.php" class="d-inline">
                                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                                <button type="submit" name="register_event" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Register
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-sm btn-outline-primary">Login to Register</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; 
                endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

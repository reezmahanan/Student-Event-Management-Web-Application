<?php
require_once __DIR__ . '/config.php';

// Get current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Normalize month/year if out of range
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// Calculate previous/next month for navigation
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }
$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

// Fetch events for the month if DB available
$events = [];
if (isset($db) && $db !== null) {
    try {
        $events_query = "SELECT * FROM events WHERE MONTH(event_date) = ? AND YEAR(event_date) = ? ORDER BY event_date ASC";
        $stmt = $db->prepare($events_query);
        $stmt->execute([$month, $year]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // If DB query fails, continue with empty events
        $events = [];
    }
}

// Generate calendar data
function generateCalendar($month, $year, $events) {
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = (int)date('t', $first_day);
    $month_name = date('F', $first_day);
    $day_of_week = (int)date('w', $first_day); // 0 (Sun) - 6 (Sat)

    $calendar = [
        'month' => $month_name,
        'year' => $year,
        'days' => []
    ];

    // Empty slots before first day
    for ($i = 0; $i < $day_of_week; $i++) {
        $calendar['days'][] = ['empty' => true];
    }

    // Fill days
    for ($day = 1; $day <= $days_in_month; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $day_events = [];
        foreach ($events as $e) {
            if (isset($e['event_date']) && $e['event_date'] === $date) {
                $day_events[] = $e;
            }
        }
        $calendar['days'][] = [
            'day' => $day,
            'date' => $date,
            'events' => $day_events,
            'has_events' => !empty($day_events)
        ];
    }

    return $calendar;
}

$calendar = generateCalendar($month, $year, $events);

// Pad trailing empty days so the grid ends on a full week (7 columns)
$total_cells = count($calendar['days']);
$remainder = $total_cells % 7;
if ($remainder !== 0) {
    $to_add = 7 - $remainder;
    for ($i = 0; $i < $to_add; $i++) {
        $calendar['days'][] = ['empty' => true];
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Calendar - <?php echo htmlspecialchars($calendar['month'] . ' ' . $calendar['year']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--accent:#667eea;--muted:#f6f7fb}
        body{background: #fff; color:#333;}
        .calendar-wrap{max-width:1100px;margin:28px auto;padding:18px}
        .cal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
        .cal-title{font-size:28px;font-weight:700}
        .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:0;border:1px solid #eee}
        .cal-weekdays{display:grid;grid-template-columns:repeat(7,1fr);background:var(--muted);}
        .cal-weekdays div{padding:10px;text-align:center;font-weight:600;border-right:1px solid #eee}
        .cal-weekdays div:last-child{border-right:0}
        .day-cell{min-height:110px;padding:8px;border-right:1px solid #eee;border-bottom:1px solid #eee;position:relative;background:#fff}
        .day-cell.empty{background:#fafbfd}
        .date-num{position:absolute;top:6px;right:8px;font-weight:600;color:#666}
        .events-list{margin-top:22px;max-height:68px;overflow:auto;padding-right:4px}
        .event-badge{display:block;background:var(--accent);color:#fff;padding:6px 8px;border-radius:6px;margin-bottom:6px;text-decoration:none;font-size:13px;box-shadow:0 3px 8px rgba(102,126,234,0.12)}
        .event-badge:hover{transform:translateY(-2px)}
        .more-indicator{display:block;background:#6c757d;color:#fff;padding:6px 8px;border-radius:6px;font-size:13px;text-align:center}
        @media (max-width:800px){.cal-grid,.cal-weekdays{grid-template-columns:repeat(7,1fr)} .day-cell{min-height:90px}}
    </style>
    <script>
        // small helper to show full event titles on hover (desktop)
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.event-badge').forEach(function(el){
                el.addEventListener('mouseenter', function(){
                    el.setAttribute('title', el.textContent.trim());
                });
            });
        });
    </script>
</head>
<body>
    <div class="calendar-wrap">
        <div class="cal-header">
            <a class="btn btn-outline-primary" href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>">&laquo; Prev</a>
            <div class="cal-title"><?php echo htmlspecialchars($calendar['month'] . ' ' . $calendar['year']); ?></div>
            <a class="btn btn-outline-primary" href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>">Next &raquo;</a>
        </div>

        <div class="cal-weekdays">
            <?php $weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; foreach ($weekdays as $wd): ?>
                <div><?php echo $wd; ?></div>
            <?php endforeach; ?>
        </div>

        <div class="cal-grid" role="grid">
            <?php foreach ($calendar['days'] as $cell): ?>
                <?php if (isset($cell['empty']) && $cell['empty']): ?>
                    <div class="day-cell empty" role="gridcell"></div>
                <?php else: ?>
                    <div class="day-cell" role="gridcell">
                        <div class="date-num"><?php echo $cell['day']; ?></div>
                        <div class="events-list">
                            <?php if (!empty($cell['events'])):
                                $count = 0;
                                foreach ($cell['events'] as $ev):
                                    $count++;
                                    if ($count <= 3): ?>
                                        <a href="../event-details.php?event_id=<?php echo urlencode($ev['event_id']); ?>" class="event-badge"><?php echo htmlspecialchars($ev['title']); ?></a>
                                    <?php endif;
                                endforeach;
                                if ($count > 3): ?>
                                    <div class="more-indicator">+<?php echo ($count - 3); ?> more</div>
                                <?php endif;
                            endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <a href="../events-calendar.php" class="btn btn-secondary">Open Full Events Page</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
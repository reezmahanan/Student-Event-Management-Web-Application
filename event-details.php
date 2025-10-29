<?php
require_once __DIR__ . '/php/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    header('Location: profile.php');
    exit();
}

// Fetch event details
try {
    $stmt = $db->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $event = false;
}

if (!$event) {
    die('Event not found');
}

// Check if user is registered for this event
try {
    $stmt = $db->prepare("SELECT * FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $registration = false;
}

if (!$registration) {
    die('You must be registered for this event to view details');
}

// Check if user already gave feedback
try {
    $stmt = $db->prepare("SELECT * FROM feedback WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $existing_feedback = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $existing_feedback = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .event-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .event-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
        }
        .event-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        .event-detail {
            padding: 15px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .btn-back {
            background: white;
            color: #667eea;
            border: 2px solid white;
        }
        .btn-back:hover {
            background: transparent;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-3">
            <a href="profile.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        <div class="event-card">
            <div class="event-header">
                <h1 class="mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
                <p class="lead mb-0"><?php echo htmlspecialchars($event['description']); ?></p>
            </div>

            <?php if (!empty($event['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($event['title']); ?>" 
                 class="event-image">
            <?php endif; ?>

            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="event-detail">
                            <h5><i class="fas fa-calendar text-primary"></i> Date & Time</h5>
                            <p class="mb-0">
                                <?php echo date('l, F j, Y', strtotime($event['event_date'])); ?><br>
                                <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="event-detail">
                            <h5><i class="fas fa-map-marker-alt text-danger"></i> Venue</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($event['venue']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="event-detail">
                            <h5><i class="fas fa-user text-success"></i> Organizer</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($event['organizer']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="event-detail">
                            <h5><i class="fas fa-users text-info"></i> Participants</h5>
                            <p class="mb-0">
                                <?php echo $event['current_participants']; ?> / <?php echo $event['max_participants']; ?> registered
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="event-detail">
                            <h5><i class="fas fa-check-circle text-success"></i> Your Registration Status</h5>
                            <p class="mb-0">
                                <span class="badge bg-<?php echo $registration['status'] === 'confirmed' ? 'success' : 'warning'; ?> fs-6">
                                    <?php echo ucfirst($registration['status']); ?>
                                </span>
                                <span class="ms-2 text-muted">
                                    Registered on <?php echo date('M j, Y g:i A', strtotime($registration['registration_date'])); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <?php if ($existing_feedback): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You already submitted feedback for this event
                        </div>
                        <a href="profile.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user"></i> Back to Profile
                        </a>
                    <?php else: ?>
                        <a href="event-feedback.php?id=<?php echo $event_id; ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-star"></i> Give Feedback
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

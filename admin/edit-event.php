<?php
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../php/login.php");
    exit();
}

$error_message = '';
$success_message = '';

// Get event ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid event ID');
}

// Fetch event
try {
    $stmt = $db->prepare("SELECT * FROM events WHERE event_id = :id");
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $event = false;
}

if (!$event) {
    die('Event not found');
}

// Handle image upload
function handleImageUpload() {
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $uploadDir = __DIR__ . '/../images/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . '_' . basename($_FILES['event_image']['name']);
        $filePath = $uploadDir . $fileName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['event_image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $filePath)) {
                // Return web-accessible path relative to project root
                return 'images/events/' . $fileName;
            }
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitization
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $venue = trim($_POST['venue'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $max_participants = (int)($_POST['max_participants'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');

    // File upload overrides image_url
    $uploaded = handleImageUpload();
    if ($uploaded) {
        $image_url = $uploaded;
    }

    if ($title === '' || $event_date === '' || $event_time === '' || $venue === '') {
        $error_message = 'Please fill in all required fields (title, date, time, venue).';
    } else {
        try {
            $update = $db->prepare("UPDATE events SET title = :title, description = :description, event_date = :event_date, event_time = :event_time, venue = :venue, max_participants = :max_participants, updated_at = NOW() WHERE event_id = :id");
            $update->execute([
                ':title' => $title,
                ':description' => $description,
                ':event_date' => $event_date,
                ':event_time' => $event_time,
                ':venue' => $venue,
                ':max_participants' => $max_participants > 0 ? $max_participants : ($event['max_participants'] ?? 0),
                ':id' => $id
            ]);

            // If image_url provided, store in a separate column if exists or in description as fallback
            // Try to add/update image_url column if present
            $cols = $db->query("SHOW COLUMNS FROM events LIKE 'image_url'")->fetchAll();
            if (!empty($cols) && $image_url !== '') {
                $imgStmt = $db->prepare("UPDATE events SET image_url = :image_url WHERE event_id = :id");
                $imgStmt->execute([':image_url' => $image_url, ':id' => $id]);
            }

            $success_message = 'Event updated successfully.';
            // Refresh event data
            $stmt = $db->prepare("SELECT * FROM events WHERE event_id = :id");
            $stmt->execute([':id' => $id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error_message = 'Failed to update event: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">EventHub Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="manage-events.php">Manage Events</a>
            <a class="nav-link" href="create-event.php">Create Event</a>
            <a class="nav-link" href="../php/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Edit Event</h4>
        </div>
        <div class="card-body">
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($event['category'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" name="event_date" class="form-control" required value="<?php echo htmlspecialchars($event['event_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Time *</label>
                        <input type="time" name="event_time" class="form-control" required value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Venue *</label>
                        <input type="text" name="venue" class="form-control" required value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Organizer</label>
                    <input type="text" name="organizer" class="form-control" value="<?php echo htmlspecialchars($event['created_by'] ?? ''); ?>">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Participants</label>
                        <input type="number" name="max_participants" class="form-control" value="<?php echo htmlspecialchars($event['max_participants'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Image URL (or upload new)</label>
                        <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($event['image_url'] ?? ''); ?>">
                        <input type="file" name="event_image" class="form-control mt-2">
                        <?php if (!empty($event['image_url'])): ?>
                            <div class="mt-2"><img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="Event Image" style="max-width:200px;max-height:120px;"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="manage-events.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

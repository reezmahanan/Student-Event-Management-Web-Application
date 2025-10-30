<?php
require_once __DIR__ . '/../php/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../php/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'Invalid request method.';
    header('Location: manage-events.php');
    exit();
}

// Basic validation
$event_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$token = $_POST['csrf_token'] ?? '';

if (empty($event_id) || empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['flash_message'] = 'Invalid CSRF token or missing event id.';
    header('Location: manage-events.php');
    exit();
}

try {
    // Start transaction
    $db->beginTransaction();

    // Remove registrations tied to this event
    $stmt = $db->prepare('DELETE FROM registrations WHERE event_id = :id');
    $stmt->execute([':id' => $event_id]);

    // Remove feedback tied to this event
    $stmt = $db->prepare('DELETE FROM feedback WHERE event_id = :id');
    $stmt->execute([':id' => $event_id]);

    // Remove volunteers tied to this event (if table exists)
    try {
        $stmt = $db->prepare('DELETE FROM volunteers WHERE event_id = :id');
        $stmt->execute([':id' => $event_id]);
    } catch (Exception $e) {
        // Table may not exist in some setups — ignore
    }

    // Remove budget entries tied to this event
    try {
        $stmt = $db->prepare('DELETE FROM event_budgets WHERE event_id = :id');
        $stmt->execute([':id' => $event_id]);
    } catch (Exception $e) {
        // Table may not exist in some setups — ignore
    }

    // Finally remove the event row
    $stmt = $db->prepare('DELETE FROM events WHERE event_id = :id');
    $stmt->execute([':id' => $event_id]);

    $db->commit();
    $_SESSION['flash_message'] = 'Event deleted successfully.';
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log('Delete event failed: ' . $e->getMessage());
    $_SESSION['flash_message'] = 'Failed to delete event. Check logs.';
}

header('Location: manage-events.php');
exit();
?>
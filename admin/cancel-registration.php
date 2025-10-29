<?php
/**
 * Cancel Event Registration
 */
require_once __DIR__ . '/../php/config.php';

// Admin access check
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get input data
$registration_id = $_POST['registration_id'] ?? null;

// Validate inputs
if (empty($registration_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Registration ID is required']);
    exit();
}

try {
    // Get registration details
    $stmt = $db->prepare("
        SELECT r.*, u.name, u.email, e.title, e.current_participants, e.max_participants 
        FROM registrations r
        JOIN users u ON r.user_id = u.user_id
        JOIN events e ON r.event_id = e.event_id
        WHERE r.reg_id = ?
    ");
    $stmt->execute([$registration_id]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registration) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Registration not found']);
        exit();
    }
    
    // Check if already cancelled
    if ($registration['status'] === 'cancelled') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'This registration is already cancelled']);
        exit();
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Update registration status
    $stmt = $db->prepare("UPDATE registrations SET status = 'cancelled' WHERE reg_id = ?");
    $stmt->execute([$registration_id]);
    
    // Decrement current_participants if status was confirmed
    if ($registration['status'] === 'confirmed') {
        $stmt = $db->prepare("
            UPDATE events 
            SET current_participants = GREATEST(0, current_participants - 1) 
            WHERE event_id = ?
        ");
        $stmt->execute([$registration['event_id']]);
    }
    
    // Send notification to user
    $message = "Your registration for the event '{$registration['title']}' has been cancelled by the administrator.";
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, message, type, is_read, created_at) 
        VALUES (?, ?, 'warning', 0, NOW())
    ");
    $stmt->execute([$registration['user_id'], $message]);
    
    // Commit transaction
    $db->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Registration cancelled successfully',
        'registration' => [
            'user_name' => $registration['name'],
            'event_title' => $registration['title']
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

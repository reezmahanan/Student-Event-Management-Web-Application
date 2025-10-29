<?php
/**
 * Delete Notification
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
$notification_id = $_POST['notification_id'] ?? null;

// Validate inputs
if (empty($notification_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

try {
    // Check if notification exists
    $stmt = $db->prepare("SELECT notification_id FROM notifications WHERE notification_id = ?");
    $stmt->execute([$notification_id]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$notification) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Notification not found']);
        exit();
    }
    
    // Delete notification
    $stmt = $db->prepare("DELETE FROM notifications WHERE notification_id = ?");
    $stmt->execute([$notification_id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notification deleted successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

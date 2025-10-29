<?php
/**
 * Send Notification to Specific User
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
$user_id = $_POST['user_id'] ?? null;
$message = trim($_POST['message'] ?? '');
$type = $_POST['type'] ?? 'general';

// Validate inputs
if (empty($user_id) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID and message are required']);
    exit();
}

try {
    // Check if user exists
    $stmt = $db->prepare("SELECT user_id, name, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Insert notification
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, message, type, is_read, created_at) 
        VALUES (?, ?, ?, 0, NOW())
    ");
    $stmt->execute([$user_id, $message, $type]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Notification sent successfully to ' . htmlspecialchars($user['name']),
        'notification_id' => $db->lastInsertId()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

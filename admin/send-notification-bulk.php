<?php
/**
 * Send Bulk Notification to All Users
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
$message = trim($_POST['message'] ?? '');
$type = $_POST['type'] ?? 'general';
$user_type = $_POST['user_type'] ?? 'all'; // all, student, organizer

// Validate inputs
if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message is required']);
    exit();
}

try {
    // Get users based on type
    if ($user_type === 'all') {
        $stmt = $db->query("SELECT user_id FROM users");
    } else {
        $stmt = $db->prepare("SELECT user_id FROM users WHERE user_type = ?");
        $stmt->execute([$user_type]);
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No users found']);
        exit();
    }
    
    // Insert notifications for all users
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, message, type, is_read, created_at) 
        VALUES (?, ?, ?, 0, NOW())
    ");
    
    $count = 0;
    foreach ($users as $user) {
        $stmt->execute([$user['user_id'], $message, $type]);
        $count++;
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Notification sent successfully to $count users",
        'count' => $count
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

<?php
/**
 * Update Volunteer Application Status
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
$volunteer_id = $_POST['volunteer_id'] ?? null;
$status = $_POST['status'] ?? null;

// Validate inputs
if (empty($volunteer_id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Volunteer ID and status are required']);
    exit();
}

// Validate status
if (!in_array($status, ['pending', 'approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status. Must be pending, approved, or rejected']);
    exit();
}

try {
    // Check if volunteer application exists
    $stmt = $db->prepare("
        SELECT v.*, u.name, u.email, e.title as event_title 
        FROM volunteers v
        JOIN users u ON v.user_id = u.user_id
        LEFT JOIN events e ON v.event_id = e.event_id
        WHERE v.volunteer_id = ?
    ");
    $stmt->execute([$volunteer_id]);
    $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$volunteer) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Volunteer application not found']);
        exit();
    }
    
    // Update status
    $stmt = $db->prepare("UPDATE volunteers SET status = ?, updated_at = NOW() WHERE volunteer_id = ?");
    $stmt->execute([$status, $volunteer_id]);
    
    // Send notification to user
    $message = '';
    $notif_type = 'info';
    
    if ($status === 'approved') {
        $event_text = $volunteer['event_id'] ? ' for ' . $volunteer['event_title'] : '';
        $message = "Great news! Your volunteer application{$event_text} has been approved. Thank you for your willingness to help!";
        $notif_type = 'success';
    } elseif ($status === 'rejected') {
        $event_text = $volunteer['event_id'] ? ' for ' . $volunteer['event_title'] : '';
        $message = "We regret to inform you that your volunteer application{$event_text} has not been approved at this time. Thank you for your interest.";
        $notif_type = 'warning';
    }
    
    if ($message) {
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, message, type, is_read, created_at) 
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$volunteer['user_id'], $message, $notif_type]);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Volunteer application ' . $status . ' successfully',
        'volunteer' => [
            'name' => $volunteer['name'],
            'email' => $volunteer['email'],
            'status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

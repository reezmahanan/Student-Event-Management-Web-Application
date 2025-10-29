<?php
/**
 * Login Processing Script
 */
require_once __DIR__ . '/config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    header('Location: ../login.php?error=required');
    exit();
}

try {
    // Check if user exists
    $stmt = $db->prepare("SELECT user_id, name, email, user_type, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: ../login.php?error=not_found');
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        header('Location: ../login.php?error=invalid');
        exit();
    }
    
    // Login successful - create session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['logged_in'] = true;
    
    // Redirect based on user type
    if ($user['user_type'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit();
    
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: ../login.php?error=server');
    exit();
}

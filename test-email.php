<?php
/**
 * Email System Test Script
 * Use this to test email functionality without registering a user
 */

require_once __DIR__ . '/php/email-config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Email System Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; }
        .info { color: #004085; padding: 10px; background: #cce5ff; border: 1px solid #b8daff; border-radius: 5px; margin-top: 20px; }
        .error { color: #721c24; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; }
        h1 { color: #667eea; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Email System Test</h1>
";

// Test data
$testName = "Test Student";
$testEmail = "test@student.com";
$testStudentId = "STU2025001";

// Send test email
try {
    $result = sendRegistrationConfirmation($testEmail, $testName, $testStudentId);
    
    if ($result) {
        echo '<div class="success">
            <strong>Success!</strong> Test email has been sent/logged.<br>
            <strong>Recipient:</strong> ' . htmlspecialchars($testEmail) . '
        </div>';
        
        echo '<div class="info">
            <strong>Where to Check:</strong><br>
            Since you\'re on localhost, the email was logged to a file:<br>
            <ul>
                <li><code>logs/emails.log</code> - Full email HTML content</li>
                <li><code>logs/email-activity.log</code> - Activity tracking</li>
            </ul>
            <strong>How to View:</strong><br>
            1. Open <code>logs/emails.log</code> in a text editor<br>
            2. Copy the HTML content<br>
            3. Save as <code>test-email.html</code> and open in browser<br>
            4. You\'ll see the beautiful email template!
        </div>';
        
    } else {
        echo '<div class="error">
            <strong>Error!</strong> Email could not be sent.<br>
            Check <code>logs/email-activity.log</code> for details.
        </div>';
    }
    
} catch (Exception $e) {
    echo '<div class="error">
        <strong>Exception:</strong> ' . htmlspecialchars($e->getMessage()) . '
    </div>';
}

echo '
    <h2>Test Details</h2>
    <pre>';
echo "Test User: {$testName}\n";
echo "Email: {$testEmail}\n";
echo "Student ID: {$testStudentId}\n";
echo "Email Enabled: " . (EmailConfig::ENABLE_EMAIL ? 'Yes' : 'No') . "\n";
echo "Log Emails: " . (EmailConfig::LOG_EMAILS ? 'Yes' : 'No') . "\n";
echo "Server: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "\n";
echo "Is Local Dev: " . (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']) ? 'Yes' : 'No');
echo '</pre>';

echo '
    <h2>Configuration</h2>
    <p>To modify email settings, edit <code>php/email-config.php</code></p>
    <ul>
        <li><strong>From Email:</strong> ' . EmailConfig::FROM_EMAIL . '</li>
        <li><strong>From Name:</strong> ' . EmailConfig::FROM_NAME . '</li>
        <li><strong>App URL:</strong> ' . EmailConfig::APP_URL . '</li>
    </ul>
    
    <h2>Documentation</h2>
    <p>For complete setup instructions, see <code>EMAIL_SETUP.md</code></p>
    
    <p style="margin-top: 30px;">
        <a href="register.php" style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px;">
            Test Real Registration
        </a>
    </p>
</body>
</html>';

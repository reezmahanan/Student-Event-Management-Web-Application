<?php
/**
 * Email Templates for EventHub
 * Contains HTML email templates for various notifications
 */

/**
 * Get the base email template wrapper
 * 
 * @param string $content Email content
 * @return string Complete HTML email
 */
function getEmailWrapper($content) {
    return '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .email-body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #667eea;
        }
        .highlight {
            color: #667eea;
            font-weight: bold;
        }
        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>EventHub</h1>
        </div>
        <div class="email-body">
            ' . $content . '
        </div>
        <div class="email-footer">
            <p><strong>EventHub - Student Event Management System</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, contact us at <a href="mailto:support@eventhub.com">support@eventhub.com</a></p>
            <p>&copy; ' . date('Y') . ' EventHub. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
}

/**
 * Registration confirmation email template
 * 
 * @param string $name User's name
 * @param string $email User's email
 * @param string $studentId Student ID
 * @return string HTML email
 */
function getRegistrationEmailTemplate($name, $email, $studentId) {
    $loginUrl = EmailConfig::APP_URL . '/login.php';
    $eventsUrl = EmailConfig::APP_URL . '/events-calendar.php';
    
    $content = '
        <h2>Welcome to EventHub!</h2>
        
        <p>Hi <span class="highlight">' . htmlspecialchars($name) . '</span>,</p>
        
        <p>Thank you for registering with EventHub! Your account has been successfully created, and you\'re now ready to explore and register for exciting campus events.</p>
        
        <div class="info-box">
            <strong>Your Account Details:</strong><br>
            <strong>Name:</strong> ' . htmlspecialchars($name) . '<br>
            <strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
            <strong>Student ID:</strong> ' . htmlspecialchars($studentId) . '<br>
        </div>
        
        <p><strong>What you can do now:</strong></p>
        <ul>
            <li>Browse upcoming events</li>
            <li>Register for events that interest you</li>
            <li>View your event registrations</li>
            <li>Provide feedback on events you attend</li>
            <li>Apply to volunteer at events</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="' . $loginUrl . '" class="button">Login to Your Account</a>
        </div>
        
        <p>Ready to discover events? <a href="' . $eventsUrl . '">Browse our events calendar</a> to see what\'s happening on campus!</p>
        
        <hr>
        
        <p><strong>Need Help?</strong></p>
        <p>If you have any questions or need assistance, feel free to reach out to our support team at <a href="mailto:' . EmailConfig::SUPPORT_EMAIL . '">' . EmailConfig::SUPPORT_EMAIL . '</a>.</p>
        
        <p>Best regards,<br>
        <strong>The EventHub Team</strong></p>
    ';
    
    return getEmailWrapper($content);
}

/**
 * Event registration confirmation email template
 * 
 * @param string $name User's name
 * @param array $eventDetails Event information
 * @return string HTML email
 */
function getEventRegistrationEmailTemplate($name, $eventDetails) {
    $eventUrl = EmailConfig::APP_URL . '/event-details.php?id=' . $eventDetails['event_id'];
    $profileUrl = EmailConfig::APP_URL . '/profile.php';
    
    $eventDate = date('l, F j, Y', strtotime($eventDetails['event_date']));
    $eventTime = date('g:i A', strtotime($eventDetails['event_date']));
    
    $content = '
        <h2>Event Registration Confirmed!</h2>
        
        <p>Hi <span class="highlight">' . htmlspecialchars($name) . '</span>,</p>
        
        <p>Great news! You have successfully registered for the following event:</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0; color: #667eea;">' . htmlspecialchars($eventDetails['title']) . '</h3>
            <strong>Date:</strong> ' . $eventDate . '<br>
            <strong>Time:</strong> ' . $eventTime . '<br>
            <strong>Location:</strong> ' . htmlspecialchars($eventDetails['location']) . '<br>
            ' . (!empty($eventDetails['description']) ? '<br><strong>About:</strong> ' . htmlspecialchars(substr($eventDetails['description'], 0, 200)) . '...' : '') . '
        </div>
        
        <p><strong>What\'s Next?</strong></p>
        <ul>
            <li>Save the date in your calendar</li>
            <li>You\'ll receive a reminder before the event</li>
            <li>Arrive on time at the venue</li>
            <li>Share your feedback after attending</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="' . $eventUrl . '" class="button">View Event Details</a>
        </div>
        
        <p>You can view all your registered events in your <a href="' . $profileUrl . '">profile dashboard</a>.</p>
        
        <hr>
        
        <p><strong>Need to Cancel?</strong></p>
        <p>If you can\'t make it, please cancel your registration through your profile so others can take your spot.</p>
        
        <p>Looking forward to seeing you at the event!</p>
        
        <p>Best regards,<br>
        <strong>The EventHub Team</strong></p>
    ';
    
    return getEmailWrapper($content);
}

/**
 * Password reset email template
 * 
 * @param string $name User's name
 * @param string $resetToken Password reset token
 * @return string HTML email
 */
function getPasswordResetEmailTemplate($name, $resetToken) {
    $resetUrl = EmailConfig::APP_URL . '/reset-password.php?token=' . $resetToken;
    
    $content = '
        <h2>Password Reset Request</h2>
        
        <p>Hi <span class="highlight">' . htmlspecialchars($name) . '</span>,</p>
        
        <p>We received a request to reset your password for your EventHub account.</p>
        
        <p>If you made this request, click the button below to reset your password:</p>
        
        <div style="text-align: center;">
            <a href="' . $resetUrl . '" class="button">Reset Your Password</a>
        </div>
        
        <p style="color: #dc3545;"><strong>Important:</strong> This link will expire in 1 hour for security reasons.</p>
        
        <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
        <p style="background-color: #f8f9fa; padding: 10px; word-break: break-all; font-size: 12px;">
            ' . $resetUrl . '
        </p>
        
        <hr>
        
        <p><strong>Didn\'t request this?</strong></p>
        <p>If you didn\'t request a password reset, please ignore this email. Your password will remain unchanged.</p>
        
        <p>For security concerns, contact us immediately at <a href="mailto:' . EmailConfig::SUPPORT_EMAIL . '">' . EmailConfig::SUPPORT_EMAIL . '</a>.</p>
        
        <p>Best regards,<br>
        <strong>The EventHub Team</strong></p>
    ';
    
    return getEmailWrapper($content);
}

/**
 * Generic notification email template
 * 
 * @param string $name User's name
 * @param string $title Notification title
 * @param string $message Notification message
 * @return string HTML email
 */
function getNotificationEmailTemplate($name, $title, $message) {
    $content = '
        <h2>' . htmlspecialchars($title) . '</h2>
        
        <p>Hi <span class="highlight">' . htmlspecialchars($name) . '</span>,</p>
        
        <div class="info-box">
            ' . nl2br(htmlspecialchars($message)) . '
        </div>
        
        <div style="text-align: center;">
            <a href="' . EmailConfig::APP_URL . '/profile.php" class="button">View Your Dashboard</a>
        </div>
        
        <p>Best regards,<br>
        <strong>The EventHub Team</strong></p>
    ';
    
    return getEmailWrapper($content);
}

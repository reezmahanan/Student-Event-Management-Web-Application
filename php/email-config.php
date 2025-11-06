<?php
/**
 * Email Configuration and Helper Functions
 * Handles email sending for EventHub application
 */

require_once __DIR__ . '/../phpmailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Configuration Settings
 */
class EmailConfig {
    // Email Settings
    const FROM_EMAIL = 'noreply@eventhub.com';
    const FROM_NAME = 'EventHub';
    const REPLY_TO = 'support@eventhub.com';
    
    // Application Settings
    const APP_NAME = 'EventHub';
    const APP_URL = 'http://localhost/project'; // Change this to your domain in production
    const SUPPORT_EMAIL = 'support@eventhub.com';
    
    // Email Features
    const ENABLE_EMAIL = true; // Set to false to disable all emails
    const LOG_EMAILS = true;   // Log emails to file (useful for debugging)
}

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body HTML email body
 * @param string $recipientName Recipient's name (optional)
 * @return bool True if email was sent successfully
 */
function sendEmail($to, $subject, $body, $recipientName = '') {
    // Check if email is enabled
    if (!EmailConfig::ENABLE_EMAIL) {
        error_log("Email sending is disabled. Would have sent to: {$to}");
        return false;
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Email settings
        $mail->isHTML(true);
        $mail->From = EmailConfig::FROM_EMAIL;
        $mail->FromName = EmailConfig::FROM_NAME;
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // Plain text version
        
        // Add recipient
        $mail->addAddress($to, $recipientName);
        
        // Send email
        $result = $mail->send();
        
        // Log if enabled
        if (EmailConfig::LOG_EMAILS) {
            logEmailActivity($to, $subject, $result ? 'Success' : 'Failed');
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        logEmailActivity($to, $subject, 'Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Log email activity to database or file
 * 
 * @param string $recipient Email recipient
 * @param string $subject Email subject
 * @param string $status Status (Success/Failed/Error)
 */
function logEmailActivity($recipient, $subject, $status) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/email-activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] To: {$recipient} | Subject: {$subject} | Status: {$status}\n";
    
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Send registration confirmation email
 * 
 * @param string $email User's email
 * @param string $name User's name
 * @param string $studentId Student ID
 * @return bool True if email was sent successfully
 */
function sendRegistrationConfirmation($email, $name, $studentId) {
    require_once __DIR__ . '/email-templates.php';
    
    $subject = "Welcome to " . EmailConfig::APP_NAME . " - Registration Successful!";
    $body = getRegistrationEmailTemplate($name, $email, $studentId);
    
    return sendEmail($email, $subject, $body, $name);
}

/**
 * Send event registration confirmation email
 * 
 * @param string $email User's email
 * @param string $name User's name
 * @param array $eventDetails Event information
 * @return bool True if email was sent successfully
 */
function sendEventRegistrationConfirmation($email, $name, $eventDetails) {
    require_once __DIR__ . '/email-templates.php';
    
    $subject = "Event Registration Confirmed - " . $eventDetails['title'];
    $body = getEventRegistrationEmailTemplate($name, $eventDetails);
    
    return sendEmail($email, $subject, $body, $name);
}

/**
 * Send password reset email
 * 
 * @param string $email User's email
 * @param string $name User's name
 * @param string $resetToken Password reset token
 * @return bool True if email was sent successfully
 */
function sendPasswordResetEmail($email, $name, $resetToken) {
    require_once __DIR__ . '/email-templates.php';
    
    $subject = "Password Reset Request - " . EmailConfig::APP_NAME;
    $body = getPasswordResetEmailTemplate($name, $resetToken);
    
    return sendEmail($email, $subject, $body, $name);
}

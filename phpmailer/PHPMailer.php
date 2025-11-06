<?php
/**
 * PHPMailer - Simplified wrapper for PHP mail() function
 * For full PHPMailer, download from: https://github.com/PHPMailer/PHPMailer
 * This is a lightweight implementation for basic email sending
 */

namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    public $isHTML = true;
    
    private $to = [];
    private $headers = [];
    
    public function __construct() {
        $this->From = 'noreply@eventhub.com';
        $this->FromName = 'EventHub';
    }
    
    public function isSMTP() {
        // Placeholder for SMTP mode
    }
    
    public function isHTML($value = true) {
        $this->isHTML = $value;
    }
    
    public function addAddress($email, $name = '') {
        $this->to[] = ['email' => $email, 'name' => $name];
    }
    
    public function send() {
        if (empty($this->to)) {
            return false;
        }
        
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        
        if ($this->isHTML) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        }
        
        $headers[] = "From: {$this->FromName} <{$this->From}>";
        $headers[] = "Reply-To: {$this->From}";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        $recipient = $this->to[0]['email'];
        $subject = $this->Subject;
        $message = $this->Body;
        
        // For local development, log emails instead of sending
        if ($this->isLocalDevelopment()) {
            return $this->logEmail($recipient, $subject, $message);
        }
        
        // Try to send email
        $result = @mail($recipient, $subject, $message, implode("\r\n", $headers));
        
        // If mail() fails, log it
        if (!$result) {
            $this->logEmail($recipient, $subject, $message);
        }
        
        return $result;
    }
    
    private function isLocalDevelopment() {
        return in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1', '::1']);
    }
    
    private function logEmail($recipient, $subject, $message) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/emails.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $logEntry = "\n" . str_repeat('=', 80) . "\n";
        $logEntry .= "Email Log Entry - {$timestamp}\n";
        $logEntry .= str_repeat('=', 80) . "\n";
        $logEntry .= "To: {$recipient}\n";
        $logEntry .= "Subject: {$subject}\n";
        $logEntry .= "Message:\n" . $message . "\n";
        $logEntry .= str_repeat('=', 80) . "\n";
        
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        return true; // Consider logged emails as "sent" for development
    }
}

class Exception extends \Exception {}

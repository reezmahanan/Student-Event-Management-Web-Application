<?php
// Database configuration (sample)
class Database {
    private $host = "localhost";      // Change to your DB host
    private $port = "3307";           // Change to your DB port
    private $db_name = "eventhub";    // Change to your DB name
    private $username = "root";       // Change to your DB username
    private $password = "";           // Change to your DB password
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            $GLOBALS['db_connected'] = true;
        } catch(PDOException $exception) {
            error_log("Database connection failed: " . $exception->getMessage());
            $GLOBALS['db_connected'] = false;
            $this->conn = null;
        }
        return $this->conn;
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site timezone configuration: default to PHP ini timezone or UTC if not set.
// You may override $SITE_TIMEZONE in this file to your local timezone (e.g., 'Asia/Dhaka').
$SITE_TIMEZONE = ini_get('date.timezone') ? ini_get('date.timezone') : 'UTC';
date_default_timezone_set($SITE_TIMEZONE);

// Helper to get a DateTime object in site timezone
function site_now() {
    global $SITE_TIMEZONE;
    return new DateTime('now', new DateTimeZone($SITE_TIMEZONE));
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Backwards-compatibility: some files expect $pdo instead of $db
$pdo = $db;
$GLOBALS['pdo'] = $pdo;
$GLOBALS['db'] = $db;

// Utility functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

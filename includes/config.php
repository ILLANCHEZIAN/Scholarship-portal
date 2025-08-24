<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'scholarship_db');

// Session configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); // Enable if using HTTPS
        ini_set('session.use_strict_mode', 1);
        
        session_start();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Database connection with error handling
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (!$conn) {
            error_log("Database connection failed: " . mysqli_connect_error());
            throw new Exception("Database connection error. Please try again later.");
        }
        
        mysqli_set_charset($conn, "utf8mb4");
    }
    
    return $conn;
}

// User role checking functions
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function isUser() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'user';
}

// Initialize secure session
startSecureSession();

// Connect to database (will throw exception on failure)
try {
    $conn = getDBConnection();
} catch (Exception $e) {
    // Handle database connection error gracefully
    error_log($e->getMessage());
    die("System maintenance in progress. Please try again later.");
}

// Security headers (if not already set)
if (!headers_sent()) {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
}

/**
 * Send an email using PHP mail function
 *
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message body (HTML allowed)
 * @param string $headers Optional additional headers
 * @return bool True if mail sent successfully, false otherwise
 */
function sendEmail($to, $subject, $message, $headers = '') {
    // Set default headers for HTML email
    $defaultHeaders = "MIME-Version: 1.0" . "\\r\\n";
    $defaultHeaders .= "Content-type:text/html;charset=UTF-8" . "\\r\\n";
    $defaultHeaders .= "From: no-reply@scholarship.com" . "\\r\\n";

    if ($headers) {
        $defaultHeaders .= $headers;
    }

    return mail($to, $subject, $message, $defaultHeaders);
}
?>



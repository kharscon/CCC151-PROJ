<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barangay_health_db');

/**
 * Get database connection
 * @return mysqli
 */
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

/**
 * Sanitize input data
 * @param mysqli $conn Database connection
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($conn, $data) {
    return $conn->real_escape_string(trim($data));
}

/**
 * Set an alert message in session
 * @param string $type Alert type: success, danger, warning, info
 * @param string $message Alert text
 */
function setAlert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear the current alert message from session
 * @return array|null
 */
function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

/**
 * Close database connection
 * @param mysqli $conn Database connection
 */
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
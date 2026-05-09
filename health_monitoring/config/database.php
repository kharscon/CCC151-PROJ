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
 * Close database connection
 * @param mysqli $conn Database connection
 */
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
<?php
// Simple mysqli connection helper for XAMPP projects
// Usage: require_once __DIR__ . '/connect.php'; $mysqli = connect_db();

function connect_db(): mysqli
{
    $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
    $DB_USER = getenv('DB_USER') ?: 'root';
    $DB_PASS = getenv('DB_PASS') ?: '';
    $DB_NAME = getenv('DB_NAME') ?: 'mau_db';

    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($mysqli->connect_errno) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

// optional quick test when accessed directly (not recommended in production)
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        $db = connect_db();
        echo json_encode(['success' => true, 'message' => 'Connected to database']);
        $db->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

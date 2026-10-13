<?php
require_once __DIR__ . '/connect.php';
$mysqli = connect_db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit;
}

$username = trim($body['username'] ?? '');
$password = $body['password'] ?? '';
$name = trim($body['name'] ?? '');
$email = trim($body['email'] ?? '');
$phone = trim($body['phone'] ?? '');
$type = trim($body['type'] ?? 'student');
$meta = $body['meta'] ?? [];

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

try {
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Username already exists']);
        exit;
    }
    $stmt->close();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $meta_json = json_encode($meta);
    $ins = $mysqli->prepare('INSERT INTO users (username, password, name, email, phone, type, meta) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $ins->bind_param('sssssss', $username, $hash, $name, $email, $phone, $type, $meta_json);
    $ins->execute();
    echo json_encode(['success' => true, 'id' => $mysqli->insert_id]);
    $ins->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}


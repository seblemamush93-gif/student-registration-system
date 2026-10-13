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

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

try {
    $stmt = $mysqli->prepare('SELECT id, username, password, name, email, phone, type, meta FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            unset($row['password']);
            $row['meta'] = json_decode($row['meta'], true);
            echo json_encode(['success' => true, 'user' => $row]);
            exit;
        }
    }
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}


<?php
require_once __DIR__ . '/connect.php';
$mysqli = connect_db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $res = $mysqli->query('SELECT id, title, content, author, type, priority, audience, expiry, created_at FROM announcements ORDER BY created_at DESC');
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['success' => true, 'announcements' => $rows]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON body']);
        exit;
    }

    $title = trim($body['title'] ?? '');
    $content = trim($body['content'] ?? '');
    $author = trim($body['author'] ?? '');
    $type = trim($body['type'] ?? 'general');
    $priority = trim($body['priority'] ?? 'normal');
    $audience = trim($body['audience'] ?? 'all');
    $expiry_raw = $body['expiry'] ?? null;
    $expiry = $expiry_raw ? trim($expiry_raw) : null;

    if ($title === '' || $content === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content required']);
        exit;
    }

    try {
        if ($expiry === null) {
            $stmt = $mysqli->prepare('INSERT INTO announcements (title, content, author, type, priority, audience) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssss', $title, $content, $author, $type, $priority, $audience);
        } else {
            $stmt = $mysqli->prepare('INSERT INTO announcements (title, content, author, type, priority, audience, expiry) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssss', $title, $content, $author, $type, $priority, $audience, $expiry);
        }
        $stmt->execute();
        echo json_encode(['success' => true, 'id' => $mysqli->insert_id]);
        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

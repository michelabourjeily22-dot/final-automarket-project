<?php

session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php'; 

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    $db = Database::getInstance()->getConnection();

    if ($method === 'GET') {
        $carId = $_GET['car_id'] ?? null;
        if (!$carId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'car_id required']);
            exit;
        }

        $currentUserId = isLoggedIn() ? (getCurrentUser()['id'] ?? 0) : 0;

$stmt = $db->prepare("
            SELECT c.id, c.car_id, c.user_id, c.comment_text, c.parent_id, 
                   c.created_at, c.updated_at,
                   u.username, u.full_name, u.profile_picture,
                   (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id) AS likes_count,
                   (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND user_id = ?) AS liked_by_user
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.car_id = ?
            ORDER BY 
    CASE WHEN c.parent_id IS NULL THEN 0 ELSE 1 END ASC,
    c.parent_id ASC,
    c.created_at ASC

        ");

        $stmt->execute([$currentUserId, $carId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $byId = [];
        $roots = [];

        foreach ($rows as $row) {
            $row['likes_count'] = (int)$row['likes_count'];
            $row['liked_by_user'] = $row['liked_by_user'] ? true : false;
            $row['replies'] = [];
            $byId[$row['id']] = $row;
        }

        foreach ($byId as $id => $c) {
            if (!empty($c['parent_id']) && isset($byId[$c['parent_id']])) {
                $byId[$c['parent_id']]['replies'][] = $c;
            } else {
                $roots[] = $c;
            }
        }

        echo json_encode(['success' => true, 'comments' => $roots]);
        exit;
    }

    if ($method === 'POST' && $action === 'toggle_like') {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;

        if (!$commentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'comment_id required']);
            exit;
        }

        $userId = getCurrentUser()['id'];

        $stmt = $db->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $stmt->execute([$commentId, $userId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $db->prepare("DELETE FROM comment_likes WHERE id = ?")->execute([$exists['id']]);
            $liked = false;
        } else {
            $db->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)")->execute([$commentId, $userId]);
            $liked = true;
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?");
        $stmt->execute([$commentId]);
        $count = (int)$stmt->fetchColumn();

        echo json_encode(['success' => true, 'liked' => $liked, 'likes_count' => $count]);
        exit;
    }

    if ($method === 'POST') {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $carId = $input['car_id'] ?? null;
        $text = trim($input['comment_text'] ?? '');
        $parentId = $input['parent_id'] ?? null;

        if (!$carId || $text === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing car_id or empty comment_text']);
            exit;
        }

        $userId = getCurrentUser()['id'];

        $stmt = $db->prepare("INSERT INTO comments (car_id, user_id, comment_text, parent_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$carId, $userId, $text, $parentId]);
        $newId = $db->lastInsertId();

        $stmt = $db->prepare("
            SELECT c.id, c.car_id, c.user_id, c.comment_text, c.parent_id,
                   c.created_at, c.updated_at,
                   u.username, u.full_name, u.profile_picture
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$newId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        $comment['likes_count'] = 0;
        $comment['liked_by_user'] = false;

        echo json_encode(['success' => true, 'comment' => $comment]);
        exit;
    }

   if ($method === 'PUT') {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;
        $text = trim($input['comment_text'] ?? '');

        if (!$commentId || $text === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'comment_id and comment_text required']);
            exit;
        }

        $userId = getCurrentUser()['id'];

        $stmt = $db->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        if ((int)$stmt->fetchColumn() !== (int)$userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Forbidden']);
            exit;
        }

        $stmt = $db->prepare("UPDATE comments SET comment_text = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$text, $commentId]);

        echo json_encode(['success' => true]);
        exit;
    }

   if ($method === 'DELETE') {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;

        if (!$commentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'comment_id required']);
            exit;
        }

        $userId = getCurrentUser()['id'];

        $stmt = $db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->execute([$commentId, $userId]);

        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;

} catch (Exception $e) {
    error_log("Comments API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
    exit;
}


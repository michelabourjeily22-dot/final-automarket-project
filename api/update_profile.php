<?php

session_start();
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$currentUser = getCurrentUser();
$userId = $currentUser['id'];

$fullName = trim($_POST['full_name'] ?? '');
$about = trim($_POST['about'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($email === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email is required']);
    exit;
}

$profilePicturePath = null;
if (!empty($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $f = $_FILES['profile_picture'];

    $allowedTypes = ['image/jpeg','image/png','image/webp'];
    if (!in_array($f['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Unsupported image type']);
        exit;
    }
    if ($f['size'] > 4 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Image too large (max 4MB)']);
        exit;
    }

    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $safeName = 'profile_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/profile_pics/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $destination = $uploadDir . $safeName;
    if (!move_uploaded_file($f['tmp_name'], $destination)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file']);
        exit;
    }

    $profilePicturePath = 'uploads/profile_pics/' . $safeName;
}

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $fields = ['full_name' => $fullName, 'updated_at' => date('Y-m-d H:i:s')];
    $sqlParts = [];
    $params = [];
    $sql = "UPDATE users SET full_name = ?, email = ?, updated_at = NOW()";

    if ($profilePicturePath) {
        $sql .= ", profile_picture = ?";
        $params[] = $profilePicturePath;
    }

    $stmt = $db->prepare($sql . " WHERE id = ?");

    $executeParams = [$fullName, $email];
    if ($profilePicturePath) $executeParams[] = $profilePicturePath;
    $executeParams[] = $userId;
    $stmt->execute($executeParams);

    $stmt = $db->prepare("SELECT id FROM user_contacts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch();
    if ($existing) {
        $stmt = $db->prepare("UPDATE user_contacts SET phone = ?, whatsapp = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$phone, $whatsapp, $email, $userId]);
    } else {
        $stmt = $db->prepare("INSERT INTO user_contacts (user_id, phone, whatsapp, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $phone, $whatsapp, $email]);
    }

    if (isset($db)) {

        try {
            $stmt = $db->prepare("UPDATE users SET about = ?, gender = ?, dob = ? WHERE id = ?");
            $stmt->execute([$about, $gender ?: null, $dob ?: null, $userId]);
        } catch (Exception $ex) {

        }
    }

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($db && $db->inTransaction()) $db->rollBack();
    error_log('update_profile error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}


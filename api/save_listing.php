<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['car_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$carId = $input['car_id'];
$action = $input['action']; // 'save' or 'unsave'
$userId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    if ($action === 'save') {
        // Check if already saved
        $checkStmt = $pdo->prepare("SELECT id FROM saved_listings WHERE user_id = ? AND car_id = ?");
        $checkStmt->execute([$userId, $carId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => true, 'action' => 'saved', 'message' => 'Already saved']);
            exit;
        }
        
        // Save listing
        $stmt = $pdo->prepare("INSERT INTO saved_listings (user_id, car_id) VALUES (?, ?)");
        $stmt->execute([$userId, $carId]);
        
        echo json_encode(['success' => true, 'action' => 'saved']);
    } else if ($action === 'unsave') {
        // Unsave listing
        $stmt = $pdo->prepare("DELETE FROM saved_listings WHERE user_id = ? AND car_id = ?");
        $stmt->execute([$userId, $carId]);
        
        echo json_encode(['success' => true, 'action' => 'unsaved']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Save listing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}


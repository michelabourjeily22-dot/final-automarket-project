<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['car_id'])) {
    echo json_encode(['success' => false, 'error' => 'Car ID required']);
    exit;
}

$carId = $input['car_id'];
$userId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // First, verify that the listing belongs to the current user
    $checkStmt = $pdo->prepare("SELECT id, user_id FROM cars WHERE id = ?");
    $checkStmt->execute([$carId]);
    $car = $checkStmt->fetch();
    
    if (!$car) {
        echo json_encode(['success' => false, 'error' => 'Listing not found']);
        exit;
    }
    
    // Check if the user owns this listing
    if ($car['user_id'] != $userId) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized: You can only delete your own listings']);
        exit;
    }
    
    // Get image paths before deleting
    $imageStmt = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ?");
    $imageStmt->execute([$carId]);
    $images = $imageStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Delete from saved_listings (cascade would handle this, but being explicit)
        $pdo->prepare("DELETE FROM saved_listings WHERE car_id = ?")->execute([$carId]);
        
        // Delete comments
        $pdo->prepare("DELETE FROM comments WHERE car_id = ?")->execute([$carId]);
        
        // Delete car images
        $pdo->prepare("DELETE FROM car_images WHERE car_id = ?")->execute([$carId]);
        
        // Delete the car listing
        $deleteStmt = $pdo->prepare("DELETE FROM cars WHERE id = ? AND user_id = ?");
        $deleteStmt->execute([$carId, $userId]);
        
        if ($deleteStmt->rowCount() > 0) {
            // Delete image files from server
            foreach ($images as $imagePath) {
                if ($imagePath && file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Listing deleted successfully']);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Failed to delete listing']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    error_log("Delete listing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}






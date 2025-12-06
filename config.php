<?php
/**
 * Configuration file
 * 
 * Database credentials should be set in config.php (copy from config.example.php)
 * DO NOT commit config.php with real credentials to version control
 */

// Database Configuration
// MAMP default MySQL port is 8889 (not 3306)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'sql100.infinityfree.com');
    define('DB_PORT', 3306); // MAMP default MySQL port
    define('DB_NAME', 'if0_40600018_new_feed');
    define('DB_USER', 'if0_40600018');
    define('DB_PASS', 'cMTHj55awoM'); // MAMP default password is 'root' (try empty string '' if this doesn't work)
}

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Application Configuration
define('SITE_NAME', 'Auto Marketplace');
define('BASE_URL', 'https://autofeedmarketplace1.lovestoblog.com');
define('DEBUG_MODE', false); // Set to false in production - temporarily true for debugging

// Google OAuth Configuration
// Get these from: https://console.cloud.google.com/apis/credentials
define('GOOGLE_CLIENT_ID', 'Dr_PUT_YOUR_OWN_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'Dr_PUT_YOUR_OWN_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth.php');

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Include required classes
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Validation.php';

// Function to get all cars as array (MySQL version)
function getAllCars() {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->query("
            SELECT c.*, 
                   u.full_name as seller_full_name,
                   u.profile_picture as seller_picture,
                   u.is_verified as seller_verified,
                   u.user_type as seller_type,
                   u.dealership_name,
                   GROUP_CONCAT(ci.image_path ORDER BY ci.display_order SEPARATOR '|||') as image_paths
            FROM cars c
            LEFT JOIN car_images ci ON c.id = ci.car_id
            LEFT JOIN users u ON c.user_id = u.id
            GROUP BY c.id
            ORDER BY u.is_verified DESC, c.created_at DESC
        ");
        
        $cars = [];
        while ($row = $stmt->fetch()) {
        $car = [
                'id' => $row['id'],
                'make' => $row['make'],
                'model' => $row['model'],
                'year' => (int)$row['year'],
                'mileage' => (int)$row['mileage'],
                'color' => $row['color'],
                'price' => (float)$row['price'],
                'images' => !empty($row['image_paths']) ? explode('|||', $row['image_paths']) : [],
                'created_at' => $row['created_at'] ?? null,
                'vehicle_type' => $row['vehicle_type'] ?? 'car',
                'transmission' => $row['transmission'] ?? null,
                'fuel_type' => $row['fuel_type'] ?? null,
                'country' => $row['country'] ?? null,
                'city' => $row['city'] ?? null,
                'user_id' => $row['user_id'] ?? null,
                'seller_name' => $row['seller_name'] ?? null,
                'seller_full_name' => $row['seller_full_name'] ?? null,
                'seller_picture' => $row['seller_picture'] ?? null,
                'seller_verified' => (bool)($row['seller_verified'] ?? false),
                'seller_type' => $row['seller_type'] ?? 'individual',
                'dealership_name' => $row['dealership_name'] ?? null
            ];
        $cars[] = $car;
    }
    
    return $cars;
    } catch (Exception $e) {
        error_log("Error getting all cars: " . $e->getMessage());
        if (DEBUG_MODE) {
            throw $e;
        }
        return [];
    }
}

// Function to get a single car by ID (MySQL version)
function getCarById($carId) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   u.full_name as seller_full_name,
                   u.profile_picture as seller_picture,
                   u.is_verified as seller_verified,
                   u.user_type as seller_type,
                   u.dealership_name,
                   u.email as seller_email,
                   COALESCE(c.seller_phone, uc.phone) as seller_phone,
                   uc.whatsapp as seller_whatsapp,
                   uc.email as seller_contact_email,
                   GROUP_CONCAT(ci.image_path ORDER BY ci.display_order SEPARATOR '|||') as image_paths
            FROM cars c
            LEFT JOIN car_images ci ON c.id = ci.car_id
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN user_contacts uc ON u.id = uc.user_id
            WHERE c.id = ?
            GROUP BY c.id
        ");
        
        $stmt->execute([$carId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return [
            'id' => $row['id'],
            'make' => $row['make'],
            'model' => $row['model'],
            'year' => (int)$row['year'],
            'mileage' => (int)$row['mileage'],
            'color' => $row['color'],
            'price' => (float)$row['price'],
            'images' => !empty($row['image_paths']) ? explode('|||', $row['image_paths']) : [],
            'created_at' => $row['created_at'] ?? null,
            'vehicle_type' => $row['vehicle_type'] ?? 'car',
            'transmission' => $row['transmission'] ?? null,
            'fuel_type' => $row['fuel_type'] ?? null,
            'country' => $row['country'] ?? null,
            'city' => $row['city'] ?? null,
            'user_id' => $row['user_id'] ?? null,
            'seller_name' => $row['seller_name'] ?? null,
            'seller_full_name' => $row['seller_full_name'] ?? null,
            'seller_picture' => $row['seller_picture'] ?? null,
            'seller_verified' => (bool)($row['seller_verified'] ?? false),
            'seller_type' => $row['seller_type'] ?? 'individual',
            'dealership_name' => $row['dealership_name'] ?? null,
            'seller_email' => $row['seller_email'] ?? $row['seller_contact_email'] ?? null,
            'seller_phone' => $row['seller_phone'] ?? null,
            'seller_whatsapp' => $row['seller_whatsapp'] ?? null
        ];
    } catch (Exception $e) {
        error_log("Error getting car by ID: " . $e->getMessage());
        if (DEBUG_MODE) {
            throw $e;
        }
    return null;
    }
}

// Function to add a new car listing (MySQL version)
function addCarListing($carData, $imagePaths) {
    $pdo = null;
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
    
    // Generate unique ID
    $carId = 'car' . time() . rand(1000, 9999);
    
        // Start transaction
        $pdo->beginTransaction();
        
        // Get user ID from session if logged in
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sellerName = null;
        
        if ($userId) {
            // Get user info for seller name
            $userStmt = $pdo->prepare("SELECT full_name, user_type, dealership_name FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();
            if ($user) {
                $sellerName = $user['user_type'] === 'dealership' && $user['dealership_name'] 
                    ? $user['dealership_name'] 
                    : $user['full_name'];
            }
        } else {
            // Guest user - generate seller number
            $sellerName = 'Seller_' . substr($carId, -4);
        }
        
        // Insert car (vehicle_type defaults to 'car' if not provided)
        $vehicleType = isset($carData['vehicle_type']) ? $carData['vehicle_type'] : 'car';
        $transmission = isset($carData['transmission']) ? $carData['transmission'] : null;
        $fuelType = isset($carData['fuel_type']) ? $carData['fuel_type'] : null;
        $country = isset($carData['country']) ? $carData['country'] : null;
        $city = isset($carData['city']) ? $carData['city'] : null;
        $sellerPhone = isset($carData['seller_phone']) ? $carData['seller_phone'] : null;
        
        $stmt = $pdo->prepare("
            INSERT INTO cars (id, make, model, vehicle_type, year, mileage, color, transmission, fuel_type, price, country, city, user_id, seller_name, seller_phone)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $carId,
            $carData['make'],
            $carData['model'],
            $vehicleType,
            $carData['year'],
            $carData['mileage'],
            $carData['color'],
            $transmission,
            $fuelType,
            $carData['price'],
            $country,
            $city,
            $userId,
            $sellerName,
            $sellerPhone
        ]);
        
        if (!$result) {
            throw new Exception("Failed to insert car record");
        }
        
        // Insert images
        if (!empty($imagePaths)) {
            $stmt = $pdo->prepare("
                INSERT INTO car_images (car_id, image_path, display_order)
                VALUES (?, ?, ?)
            ");
            
            foreach ($imagePaths as $order => $imagePath) {
                $imgResult = $stmt->execute([$carId, $imagePath, $order]);
                if (!$imgResult) {
                    throw new Exception("Failed to insert image record for: " . $imagePath);
                }
            }
        }
        
        $pdo->commit();
    
    return $carId;
    } catch (PDOException $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("PDO Error adding car listing: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        if (DEBUG_MODE) {
            throw $e;
        }
        return false;
    } catch (Exception $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error adding car listing: " . $e->getMessage());
        if (DEBUG_MODE) {
            throw $e;
        }
        return false;
    }
}

// Function to validate uploaded images (using Validation class)
function validateImage($file) {
    $validation = Validation::validateImage($file);
    
    if (!$validation['valid']) {
        return [
            'success' => false,
            'message' => implode(', ', $validation['errors'])
        ];
    }
    
    return ['success' => true];
}

// Function to upload image
function uploadImage($file) {
    $validation = validateImage($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_', true) . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => 'uploads/' . $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper function to get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting current user: " . $e->getMessage());
        return null;
    }
}

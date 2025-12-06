<?php
/**
 * Process Listing Form Submission
 * 
 * Handles car listing creation with server-side validation
 * Uses MySQL database and prepared statements
 */

session_start();
require_once 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate car data using Validation class
    $validation = Validation::validateCarListing($_POST);
    
    if (!$validation['valid']) {
        $errors = array_values($validation['errors']);
    }
    
    // Validate and upload images
    $imagePaths = [];
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $fileCount = count($_FILES['images']['name']);
        
        // Process all uploaded files
        $successfulUploads = 0;
        for ($i = 0; $i < $fileCount; $i++) {
            // Skip empty file names
            if (empty($_FILES['images']['name'][$i])) {
                continue;
            }
            
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i]
                ];
                
                $result = uploadImage($file);
                if ($result['success']) {
                    $imagePaths[] = $result['path'];
                    $successfulUploads++;
                } else {
                    $errors[] = $result['message'] . ' for ' . htmlspecialchars($file['name']);
                }
            } elseif ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errors[] = 'Upload error for ' . htmlspecialchars($_FILES['images']['name'][$i]) . ' (Error code: ' . $_FILES['images']['error'][$i] . ')';
            }
        }
        
        // Require at least one successfully uploaded image
        if ($successfulUploads === 0) {
            if (empty($errors)) {
                $errors[] = 'At least one image is required';
            }
        }
    } else {
        $errors[] = 'At least one image is required';
    }
    
    // If no errors, add the listing to MySQL
    if (empty($errors) && !empty($imagePaths) && $validation['valid']) {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            // Check if tables exist
            $tablesCheck = $pdo->query("SHOW TABLES LIKE 'cars'");
            if ($tablesCheck->rowCount() == 0) {
                $errors[] = 'Database tables not found. Please run the schema.sql file to create the tables.';
            } else {
                $carId = addCarListing($validation['data'], $imagePaths);
                
                if ($carId) {
        $success = true;
        // Redirect to homepage after successful submission
        header('Location: index.php?success=1');
        exit;
                } else {
                    $errors[] = 'Failed to create listing. Please check database connection and try again.';
                    if (DEBUG_MODE) {
                        $errors[] = 'Check PHP error logs for details.';
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database error creating listing: " . $e->getMessage());
            $errorMsg = DEBUG_MODE 
                ? 'Database Error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')'
                : 'Database connection failed. Please check your database configuration in config.php';
            $errors[] = $errorMsg;
        } catch (Exception $e) {
            error_log("Error creating listing: " . $e->getMessage());
            $errorMsg = DEBUG_MODE ? $e->getMessage() : 'An error occurred while creating the listing. Please check your database connection.';
            $errors[] = $errorMsg;
        }
    }
}

// If there are errors or form wasn't submitted properly, redirect back
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    
    // Check which page the form was submitted from
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    
    // Always redirect to add_post.php (add_listing.php is deprecated)
    header('Location: add_post.php');
    exit;
}
?>


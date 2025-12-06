<?php
/**
 * Configuration Example File
 * 
 * Copy this file to config.php and update with your database credentials
 * 
 * DO NOT commit config.php to version control
 */

// Database Configuration
// MAMP default MySQL port is 8889 (not 3306)
define('DB_HOST', 'localhost');
define('DB_PORT', 8889); // MAMP default MySQL port (change to 3306 for standard MySQL)
define('DB_NAME', 'auto_marketplace');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // MAMP default password (try empty string '' if this doesn't work)

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Application Configuration
define('SITE_NAME', 'Auto Marketplace');
define('BASE_URL', 'http://localhost'); // Update for production

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

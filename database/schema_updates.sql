-- Schema Updates for New Features
-- Run this after the base schema.sql

-- Update users table for Google OAuth
-- Note: Run these one at a time if columns already exist
ALTER TABLE users 
ADD COLUMN google_uid VARCHAR(255) UNIQUE,
ADD COLUMN google_email VARCHAR(255),
ADD COLUMN profile_picture VARCHAR(500),
ADD COLUMN user_type ENUM('individual', 'dealership') DEFAULT 'individual',
ADD COLUMN is_verified BOOLEAN DEFAULT FALSE,
ADD COLUMN verified_at TIMESTAMP NULL;

ALTER TABLE users
ADD INDEX idx_google_uid (google_uid),
ADD INDEX idx_user_type (user_type),
ADD INDEX idx_is_verified (is_verified);

-- Add dealership fields to users table
ALTER TABLE users
ADD COLUMN dealership_name VARCHAR(200),
ADD COLUMN dealership_logo VARCHAR(500),
ADD COLUMN dealership_location VARCHAR(255),
ADD COLUMN dealership_hours TEXT;

-- Add location fields to cars table
ALTER TABLE cars
ADD COLUMN country VARCHAR(100),
ADD COLUMN city VARCHAR(100),
ADD COLUMN user_id INT,
ADD COLUMN seller_name VARCHAR(200);

ALTER TABLE cars
ADD INDEX idx_country (country),
ADD INDEX idx_city (city),
ADD INDEX idx_user_id (user_id);

-- Add foreign key separately (may need to drop if exists)
ALTER TABLE cars
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Create saved_listings table (wishlist)
CREATE TABLE IF NOT EXISTS saved_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_car (user_id, car_id),
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_car_id (car_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create likes table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_car_like (user_id, car_id),
    INDEX idx_car_id (car_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_contacts table (for contact seller feature)
CREATE TABLE IF NOT EXISTS user_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    email VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


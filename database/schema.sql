-- Auto Marketplace Database Schema
-- Created: 2024-01-XX
-- Description: MySQL schema to replace XML/DTD storage

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS auto_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE auto_marketplace;

-- Cars table (main listings)
CREATE TABLE IF NOT EXISTS cars (
    id VARCHAR(50) PRIMARY KEY,
    vehicle_type ENUM('car', 'motorcycle', 'truck') DEFAULT 'car',
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    mileage INT NOT NULL,
    color VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    transmission ENUM('Automatic', 'Manual', 'Steptronic') DEFAULT 'Automatic',
    fuel_type ENUM('Electric', 'Benzine', 'Hybrid', 'Diesel') DEFAULT 'Benzine',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_make (make),
    INDEX idx_model (model),
    INDEX idx_year (year),
    INDEX idx_price (price),
    INDEX idx_created (created_at),
    INDEX idx_vehicle_type (vehicle_type),
    INDEX idx_transmission (transmission),
    INDEX idx_fuel_type (fuel_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Car images table (one-to-many relationship)
CREATE TABLE IF NOT EXISTS car_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id VARCHAR(50) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_car_id (car_id),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table (for future authentication/profile features)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    full_name VARCHAR(200),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Search metadata table (for analytics and optimization)
CREATE TABLE IF NOT EXISTS search_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    search_query TEXT,
    filters JSON,
    results_count INT,
    user_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


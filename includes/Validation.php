<?php
/**
 * Validation Helper Class
 * 
 * Server-side validation and sanitization functions
 */

class Validation {
    
    /**
     * Validate and sanitize car listing data
     */
    public static function validateCarListing($data) {
        $errors = [];
        $sanitized = [];
        
        // Make
        if (empty($data['make'])) {
            $errors['make'] = 'Make is required';
        } else {
            $sanitized['make'] = self::sanitizeString($data['make'], 100);
            if (strlen($sanitized['make']) < 1) {
                $errors['make'] = 'Make must be at least 1 character';
            }
        }
        
        // Model
        if (empty($data['model'])) {
            $errors['model'] = 'Model is required';
        } else {
            $sanitized['model'] = self::sanitizeString($data['model'], 100);
            if (strlen($sanitized['model']) < 1) {
                $errors['model'] = 'Model must be at least 1 character';
            }
        }
        
        // Vehicle Type
        if (empty($data['vehicle_type'])) {
            $sanitized['vehicle_type'] = 'car'; // Default to car
        } else {
            $validTypes = ['car', 'truck', 'motorcycle'];
            $vehicleType = strtolower(trim($data['vehicle_type']));
            if (in_array($vehicleType, $validTypes)) {
                $sanitized['vehicle_type'] = $vehicleType;
            } else {
                $errors['vehicle_type'] = 'Vehicle type must be car, truck, or motorcycle';
            }
        }
        
        // Year
        if (empty($data['year'])) {
            $errors['year'] = 'Year is required';
        } else {
            $year = filter_var($data['year'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1900, 'max_range' => (int)date('Y') + 1]
            ]);
            if ($year === false) {
                $errors['year'] = 'Year must be between 1900 and ' . (date('Y') + 1);
            } else {
                $sanitized['year'] = $year;
            }
        }
        
        // Mileage
        if (empty($data['mileage'])) {
            $errors['mileage'] = 'Mileage is required';
        } else {
            $mileage = filter_var($data['mileage'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0]
            ]);
            if ($mileage === false) {
                $errors['mileage'] = 'Mileage must be a positive number';
            } else {
                $sanitized['mileage'] = $mileage;
            }
        }
        
        // Transmission
        if (empty($data['transmission'])) {
            $errors['transmission'] = 'Transmission is required';
        } else {
            $validTransmissions = ['Automatic', 'Manual', 'Steptronic'];
            $transmission = trim($data['transmission']);
            if (in_array($transmission, $validTransmissions)) {
                $sanitized['transmission'] = $transmission;
            } else {
                $errors['transmission'] = 'Transmission must be Automatic, Manual, or Steptronic';
            }
        }
        
        // Fuel Type
        if (empty($data['fuel_type'])) {
            $errors['fuel_type'] = 'Fuel type is required';
        } else {
            $validFuelTypes = ['Electric', 'Benzine', 'Hybrid', 'Diesel'];
            $fuelType = trim($data['fuel_type']);
            if (in_array($fuelType, $validFuelTypes)) {
                $sanitized['fuel_type'] = $fuelType;
            } else {
                $errors['fuel_type'] = 'Fuel type must be Electric, Benzine, Hybrid, or Diesel';
            }
        }
        
        // Color
        if (empty($data['color'])) {
            $errors['color'] = 'Color is required';
        } else {
            $sanitized['color'] = self::sanitizeString($data['color'], 50);
            if (strlen($sanitized['color']) < 1) {
                $errors['color'] = 'Color must be at least 1 character';
            }
        }
        
        // Price
        if (empty($data['price'])) {
            $errors['price'] = 'Price is required';
        } else {
            $price = filter_var($data['price'], FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]);
            if ($price === false) {
                $errors['price'] = 'Price must be a positive number';
            } else {
                // Preserve exact value - only round if there are more than 2 decimal places
                // This allows whole numbers like 27890 to be stored exactly as entered
                $sanitized['price'] = $price;
            }
        }

        // City (required for listing location)
        if (empty($data['city'])) {
            $errors['city'] = 'City is required';
        } else {
            $sanitized['city'] = self::sanitizeString($data['city'], 100);
        }
        
        // Seller Phone (required)
        if (empty($data['seller_phone'])) {
            $errors['seller_phone'] = 'Phone number is required';
        } else {
            $phone = self::sanitizeString($data['seller_phone'], 30);
            // Remove any non-digit characters except + at the start
            $phone = preg_replace('/[^\d+]/', '', $phone);
            // Remove + if present (we'll store without country code)
            $phone = ltrim($phone, '+');
            // Basic phone number validation - just digits, 7-15 digits
            if (!preg_match('/^\d{7,15}$/', $phone)) {
                $errors['seller_phone'] = 'Phone number must be 7-15 digits';
            } else {
                $sanitized['seller_phone'] = $phone;
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Validate search filters
     */
    public static function validateSearchFilters($filters) {
        $sanitized = [];
        
        if (isset($filters['vehicleType']) && is_array($filters['vehicleType'])) {
            $validTypes = ['car', 'truck', 'motorcycle'];
            $sanitized['vehicleType'] = array_filter($filters['vehicleType'], function($type) use ($validTypes) {
                return in_array(strtolower($type), $validTypes);
            });
        }
        
        if (isset($filters['make']) && is_array($filters['make'])) {
            $sanitized['make'] = array_map(function($m) {
                return self::sanitizeString($m, 100);
            }, $filters['make']);
        }
        
        if (isset($filters['model']) && is_array($filters['model'])) {
            $sanitized['model'] = array_map(function($m) {
                return self::sanitizeString($m, 100);
            }, $filters['model']);
        }
        
        if (isset($filters['transmission']) && is_array($filters['transmission'])) {
            $validTransmissions = ['Automatic', 'Manual', 'Steptronic'];
            $sanitized['transmission'] = array_filter($filters['transmission'], function($t) use ($validTransmissions) {
                return in_array($t, $validTransmissions);
            });
        }
        
        if (isset($filters['fuel_type']) && is_array($filters['fuel_type'])) {
            $validFuelTypes = ['Benzine', 'Diesel', 'Electric', 'Hybrid'];
            $sanitized['fuel_type'] = array_filter($filters['fuel_type'], function($f) use ($validFuelTypes) {
                return in_array($f, $validFuelTypes);
            });
        }
        
        if (isset($filters['color']) && is_array($filters['color'])) {
            $validColors = ['White', 'Black', 'Grey', 'Silver', 'Blue', 'Red'];
            $sanitized['color'] = array_filter($filters['color'], function($c) use ($validColors) {
                return in_array($c, $validColors);
            });
        }
        
        // Year range filters
        if (isset($filters['minYear'])) {
            $minYear = filter_var($filters['minYear'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1900, 'max_range' => (int)date('Y') + 1]
            ]);
            if ($minYear !== false) {
                $sanitized['minYear'] = $minYear;
            }
        }
        
        if (isset($filters['maxYear'])) {
            $maxYear = filter_var($filters['maxYear'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1900, 'max_range' => (int)date('Y') + 1]
            ]);
            if ($maxYear !== false) {
                $sanitized['maxYear'] = $maxYear;
            }
        }
        
        // Price range filters
        if (isset($filters['minPrice'])) {
            $minPrice = filter_var($filters['minPrice'], FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]);
            if ($minPrice !== false) {
                $sanitized['minPrice'] = $minPrice;
            }
        }
        
        if (isset($filters['maxPrice'])) {
            $maxPrice = filter_var($filters['maxPrice'], FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]);
            if ($maxPrice !== false) {
                $sanitized['maxPrice'] = $maxPrice;
            }
        }
        
        // Mileage range filters
        if (isset($filters['minMileage'])) {
            $minMileage = filter_var($filters['minMileage'], FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]);
            if ($minMileage !== false) {
                $sanitized['minMileage'] = $minMileage;
            }
        }
        
        if (isset($filters['maxMileage'])) {
            $maxMileage = filter_var($filters['maxMileage'], FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]);
            if ($maxMileage !== false) {
                $sanitized['maxMileage'] = $maxMileage;
            }
        }
        
        // Mileage unit
        if (isset($filters['mileageUnit'])) {
            $validUnits = ['miles', 'km'];
            $unit = strtolower($filters['mileageUnit']);
            if (in_array($unit, $validUnits)) {
                $sanitized['mileageUnit'] = $unit;
            } else {
                $sanitized['mileageUnit'] = 'miles'; // default
            }
        }
        
        // Note: Transmission, fuel_type, and color are now handled as arrays above (lines 154-170)
        
        if (isset($filters['query'])) {
            $sanitized['query'] = self::sanitizeString($filters['query'], 255);
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input, $maxLength = 255) {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        
        if ($maxLength > 0 && strlen($sanitized) > $maxLength) {
            $sanitized = substr($sanitized, 0, $maxLength);
        }
        
        return $sanitized;
    }
    
    /**
     * Validate uploaded image file
     */
    public static function validateImage($file) {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload error occurred';
            return ['valid' => false, 'errors' => $errors];
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds maximum allowed (' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)';
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $errors[] = 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS);
        }
        
        // Check MIME type (fallbacks for environments without fileinfo extension)
        $mimeType = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
            }
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($file['tmp_name']);
        }
        
        $allowedMimes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        if ($mimeType !== null && !in_array($mimeType, $allowedMimes)) {
            $errors[] = 'Invalid file MIME type';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
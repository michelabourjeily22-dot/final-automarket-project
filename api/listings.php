<?php
header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/../config.php';
/**
 * Listings API Endpoint
 * 
 * CRUD operations for car listings
 * Supports GET (list all), POST (create), PUT (update), DELETE
 * Returns XML response
 * Accepts JSON POST data
 */

/**
 * Helper function to escape XML content
 */
function escapeXml($value) {
    return htmlspecialchars((string)$value, ENT_XML1, 'UTF-8');
}

/**
 * Helper function to convert car array to XML
 */
function carToXML($car) {
    $xml = '    <car>' . "\n";
    $xml .= '      <id>' . escapeXml($car['id'] ?? '') . '</id>' . "\n";
    $xml .= '      <make>' . escapeXml($car['make'] ?? '') . '</make>' . "\n";
    $xml .= '      <model>' . escapeXml($car['model'] ?? '') . '</model>' . "\n";
    $xml .= '      <year>' . (int)($car['year'] ?? 0) . '</year>' . "\n";
    $xml .= '      <price>' . number_format((float)($car['price'] ?? 0), 2, '.', '') . '</price>' . "\n";
    $xml .= '      <mileage>' . (int)($car['mileage'] ?? 0) . '</mileage>' . "\n";
    $xml .= '      <color>' . escapeXml($car['color'] ?? '') . '</color>' . "\n";
    $xml .= '      <fuel>' . escapeXml($car['fuel_type'] ?? '') . '</fuel>' . "\n";
    $xml .= '      <transmission>' . escapeXml($car['transmission'] ?? '') . '</transmission>' . "\n";
    $xml .= '      <vehicle_type>' . escapeXml($car['vehicle_type'] ?? 'car') . '</vehicle_type>' . "\n";
    
    if (!empty($car['images']) && is_array($car['images'])) {
        foreach ($car['images'] as $image) {
            if ($image) {
                $xml .= '      <image>' . escapeXml($image) . '</image>' . "\n";
            }
        }
    }
    
    if (!empty($car['created_at'])) {
        $xml .= '      <created_at>' . escapeXml($car['created_at']) . '</created_at>' . "\n";
    }
    
    $xml .= '    </car>' . "\n";
    return $xml;
}

/**
 * Helper function to output XML response
 */
function outputXMLResponse($success, $data = null, $error = null, $message = null, $id = null, $count = null, $errors = null) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>' . ($success ? 'true' : 'false') . '</success>' . "\n";
    
    if ($error) {
        $xml .= '  <error>' . escapeXml($error) . '</error>' . "\n";
    }
    
    if ($message) {
        $xml .= '  <message>' . escapeXml($message) . '</message>' . "\n";
    }
    
    if ($id) {
        $xml .= '  <id>' . escapeXml($id) . '</id>' . "\n";
    }
    
    if ($count !== null) {
        $xml .= '  <count>' . (int)$count . '</count>' . "\n";
    }
    
    if ($errors && is_array($errors)) {
        $xml .= '  <errors>' . "\n";
        foreach ($errors as $err) {
            $xml .= '    <error>' . escapeXml($err) . '</error>' . "\n";
        }
        $xml .= '  </errors>' . "\n";
    }
    
    if ($data) {
        if (isset($data['id']) || isset($data['make'])) {
            // Single car
            $xml .= '  <car>' . "\n";
            $xml .= carToXML($data);
            $xml .= '  </car>' . "\n";
            $xml .= '  <data>' . "\n";
            $xml .= carToXML($data);
            $xml .= '  </data>' . "\n";
        } elseif (is_array($data) && count($data) > 0 && (isset($data[0]['id']) || isset($data[0]['make']))) {
            // Array of cars
            $xml .= '  <cars>' . "\n";
            $xml .= '  <data>' . "\n";
            foreach ($data as $car) {
                $xml .= carToXML($car);
            }
            $xml .= '  </data>' . "\n";
            $xml .= '  </cars>' . "\n";
        }
    }
    
    $xml .= '</response>';
    return $xml;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    switch ($method) {
        case 'GET':
            // Get all listings or single listing
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            if ($id) {
                $car = getCarById($id);
                if ($car) {
                    echo trim(outputXMLResponse(true, $car));
                } else {
                    http_response_code(404);
                    echo trim(outputXMLResponse(false, null, 'Listing not found'));
                }
            } else {
                $cars = getAllCars();
                echo trim(outputXMLResponse(true, $cars, null, null, null, count($cars)));
            }
            break;
            
        case 'POST':
            // Create new listing
            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, true);
            
            if (!$data) {
                http_response_code(400);
                echo trim(outputXMLResponse(false, null, 'Invalid JSON data'));
                break;
            }
            
            // Validate data
            $validation = Validation::validateCarListing($data);
            
            if (!$validation['valid']) {
                http_response_code(400);
                echo trim(outputXMLResponse(false, null, 'Validation failed', null, null, null, $validation['errors']));
                break;
            }
            
            // Handle image uploads (if provided)
            $imagePaths = [];
            if (isset($data['images']) && is_array($data['images'])) {
                // In a real implementation, you'd handle base64 images or file uploads
                // For now, assume image paths are provided
                $imagePaths = $data['images'];
            }
            
            $carId = addCarListing($validation['data'], $imagePaths);
            
            if ($carId) {
                http_response_code(201);
                echo trim(outputXMLResponse(true, null, null, 'Listing created successfully', $carId));
            } else {
                http_response_code(500);
                echo trim(outputXMLResponse(false, null, 'Failed to create listing'));
            }
            break;
            
        case 'PUT':
        case 'DELETE':
            // Update and delete not implemented yet
            http_response_code(501);
            echo trim(outputXMLResponse(false, null, 'Not implemented'));
            break;
            
        default:
            http_response_code(405);
            echo trim(outputXMLResponse(false, null, 'Method not allowed'));
    }
    
} catch (Exception $e) {
    error_log("Listings API error: " . $e->getMessage());
    http_response_code(500);
    echo trim(outputXMLResponse(false, null, DEBUG_MODE ? $e->getMessage() : 'An error occurred'));
}
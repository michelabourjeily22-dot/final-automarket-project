<?php
header('Content-Type: application/xml; charset=utf-8');
require_once '../config.php';
/**
 * Cars API Endpoint
 * GET /api/cars.php - Get all cars
 * GET /api/cars.php?id={id} - Get single car by ID
 * GET /api/cars.php?make={make}&year={year}&max_price={price} - Filter cars
 * Returns XML response
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

try {
    $carId = isset($_GET['id']) ? $_GET['id'] : null;
    $make = isset($_GET['make']) ? $_GET['make'] : null;
    $year = isset($_GET['year']) ? $_GET['year'] : null;
    $maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

    // Get single car by ID
    if ($carId) {
        $car = getCarById($carId);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<response>' . "\n";
        
        if ($car) {
            $xml .= '  <success>true</success>' . "\n";
            $xml .= '  <timestamp>' . escapeXml(date('Y-m-d H:i:s')) . '</timestamp>' . "\n";
            $xml .= '  <car>' . "\n";
            $xml .= carToXML($car);
            $xml .= '  </car>' . "\n";
        } else {
            http_response_code(404);
            $xml .= '  <success>false</success>' . "\n";
            $xml .= '  <error>Car not found</error>' . "\n";
        }
        
        $xml .= '</response>';
        echo trim($xml);
        exit;
    }

    // Get all cars
    $cars = getAllCars();
    
    // Apply filters if provided
    if ($make || $year || $maxPrice !== null) {
        $cars = array_filter($cars, function($car) use ($make, $year, $maxPrice) {
            if ($make && strtolower($car['make']) !== strtolower($make)) {
                return false;
            }
            if ($year && $car['year'] != $year) {
                return false;
            }
            if ($maxPrice !== null && floatval($car['price']) > $maxPrice) {
                return false;
            }
            return true;
        });
        $cars = array_values($cars); // Re-index array
    }
    
    // Prepare XML response
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>true</success>' . "\n";
    $xml .= '  <count>' . count($cars) . '</count>' . "\n";
    $xml .= '  <timestamp>' . escapeXml(date('Y-m-d H:i:s')) . '</timestamp>' . "\n";
    $xml .= '  <cars>' . "\n";
    
    foreach ($cars as $car) {
        $xml .= carToXML($car);
    }
    
    $xml .= '  </cars>' . "\n";
    $xml .= '</response>';
    
    echo trim($xml);
    
} catch (Exception $e) {
    http_response_code(500);
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>false</success>' . "\n";
    $xml .= '  <error>Failed to retrieve cars</error>' . "\n";
    $xml .= '  <message>' . escapeXml($e->getMessage()) . '</message>' . "\n";
    $xml .= '</response>';
    
    echo trim($xml);
}
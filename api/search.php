<?php
header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/../config.php';
/**
 * Search API Endpoint
 * 
 * Server-side search and filtering for car listings
 * Returns XML response with filtered results
 */

// Only allow GET and POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo trim('<?xml version="1.0" encoding="UTF-8"?><response><success>false</success><error>Method not allowed</error></response>');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Get search parameters from JSON POST body or fall back to GET/POST for backward compatibility
    $jsonData = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rawData = file_get_contents('php://input');
        $jsonData = json_decode($rawData, true);
    }
    
    // Extract parameters from JSON or fall back to GET/POST
    $query = $jsonData['q'] ?? (isset($_GET['q']) ? $_GET['q'] : (isset($_POST['q']) ? $_POST['q'] : ''));
    $vehicleTypes = $jsonData['vehicleTypes'] ?? (isset($_GET['vehicleTypes']) ? $_GET['vehicleTypes'] : (isset($_POST['vehicleTypes']) ? $_POST['vehicleTypes'] : []));
    $makes = $jsonData['makes'] ?? (isset($_GET['makes']) ? $_GET['makes'] : (isset($_POST['makes']) ? $_POST['makes'] : []));
    $minYear = $jsonData['minYear'] ?? (isset($_GET['minYear']) ? $_GET['minYear'] : (isset($_POST['minYear']) ? $_POST['minYear'] : null));
    $maxYear = $jsonData['maxYear'] ?? (isset($_GET['maxYear']) ? $_GET['maxYear'] : (isset($_POST['maxYear']) ? $_POST['maxYear'] : null));
    $maxPrice = $jsonData['maxPrice'] ?? (isset($_GET['maxPrice']) ? $_GET['maxPrice'] : (isset($_POST['maxPrice']) ? $_POST['maxPrice'] : null));
    $minPrice = $jsonData['minPrice'] ?? (isset($_GET['minPrice']) ? $_GET['minPrice'] : (isset($_POST['minPrice']) ? $_POST['minPrice'] : null));
    $minMileage = $jsonData['minMileage'] ?? (isset($_GET['minMileage']) ? $_GET['minMileage'] : (isset($_POST['minMileage']) ? $_POST['minMileage'] : null));
    $maxMileage = $jsonData['maxMileage'] ?? (isset($_GET['maxMileage']) ? $_GET['maxMileage'] : (isset($_POST['maxMileage']) ? $_POST['maxMileage'] : null));
    $mileageUnit = $jsonData['mileageUnit'] ?? (isset($_GET['mileageUnit']) ? $_GET['mileageUnit'] : (isset($_POST['mileageUnit']) ? $_POST['mileageUnit'] : 'miles'));
    $models = $jsonData['models'] ?? (isset($_GET['models']) ? $_GET['models'] : (isset($_POST['models']) ? $_POST['models'] : []));
    $transmission = $jsonData['transmission'] ?? (isset($_GET['transmission']) ? $_GET['transmission'] : (isset($_POST['transmission']) ? $_POST['transmission'] : []));
    $fuelType = $jsonData['fuel_type'] ?? (isset($_GET['fuel_type']) ? $_GET['fuel_type'] : (isset($_POST['fuel_type']) ? $_POST['fuel_type'] : []));
    $color = $jsonData['color'] ?? (isset($_GET['color']) ? $_GET['color'] : (isset($_POST['color']) ? $_POST['color'] : []));
    
    // Validate and sanitize inputs
    $filters = [
        'query' => $query,
        'vehicleType' => is_array($vehicleTypes) ? $vehicleTypes : [],
        'make' => is_array($makes) ? $makes : [],
        'minYear' => $minYear,
        'maxYear' => $maxYear,
        'maxPrice' => $maxPrice,
        'minPrice' => $minPrice,
        'minMileage' => $minMileage,
        'maxMileage' => $maxMileage,
        'mileageUnit' => $mileageUnit,
        'model' => is_array($models) ? $models : [],
        'transmission' => is_array($transmission) ? $transmission : (empty($transmission) ? [] : [$transmission]),
        'fuel_type' => is_array($fuelType) ? $fuelType : (empty($fuelType) ? [] : [$fuelType]),
        'color' => is_array($color) ? $color : (empty($color) ? [] : [$color])
    ];
    
    $sanitizedFilters = Validation::validateSearchFilters($filters);
    
    // Build query
    $sql = "
        SELECT c.*, 
               GROUP_CONCAT(ci.image_path ORDER BY ci.display_order SEPARATOR '|||') as image_paths
        FROM cars c
        LEFT JOIN car_images ci ON c.id = ci.car_id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Search query - we'll apply smart matching (normalization + fuzzy) in PHP after fetching results
    // Remove SQL-based text search so we can do advanced fuzzy matching in PHP
    $searchQuery = !empty($sanitizedFilters['query']) ? $sanitizedFilters['query'] : null;
    
    // Filter by vehicle type
    if (!empty($sanitizedFilters['vehicleType']) && is_array($sanitizedFilters['vehicleType'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['vehicleType']) - 1) . '?';
        $sql .= " AND COALESCE(c.vehicle_type, 'car') IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['vehicleType']);
    }
    
    // Filter by makes
    if (!empty($sanitizedFilters['make']) && is_array($sanitizedFilters['make'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['make']) - 1) . '?';
        $sql .= " AND c.make IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['make']);
    }
    
    // Filter by models
    if (!empty($sanitizedFilters['model']) && is_array($sanitizedFilters['model'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['model']) - 1) . '?';
        $sql .= " AND c.model IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['model']);
    }
    
    // Filter by year range
    if (isset($sanitizedFilters['minYear'])) {
        $sql .= " AND c.year >= ?";
        $params[] = $sanitizedFilters['minYear'];
    }
    
    if (isset($sanitizedFilters['maxYear'])) {
        $sql .= " AND c.year <= ?";
        $params[] = $sanitizedFilters['maxYear'];
    }
    
    // Filter by price range
    if (isset($sanitizedFilters['minPrice'])) {
        $sql .= " AND c.price >= ?";
        $params[] = $sanitizedFilters['minPrice'];
    }
    
    if (isset($sanitizedFilters['maxPrice'])) {
        $sql .= " AND c.price <= ?";
        $params[] = $sanitizedFilters['maxPrice'];
    }
    
    // Filter by mileage range
    if (isset($sanitizedFilters['minMileage']) || isset($sanitizedFilters['maxMileage'])) {
        $mileageUnit = $sanitizedFilters['mileageUnit'] ?? 'miles';
        // Convert km to miles for database comparison (database stores in miles)
        if ($mileageUnit === 'km') {
            if (isset($sanitizedFilters['minMileage'])) {
                $sql .= " AND c.mileage >= ?";
                $params[] = round($sanitizedFilters['minMileage'] / 1.60934);
            }
            if (isset($sanitizedFilters['maxMileage'])) {
                $sql .= " AND c.mileage <= ?";
                $params[] = round($sanitizedFilters['maxMileage'] / 1.60934);
            }
        } else {
            if (isset($sanitizedFilters['minMileage'])) {
                $sql .= " AND c.mileage >= ?";
                $params[] = $sanitizedFilters['minMileage'];
            }
            if (isset($sanitizedFilters['maxMileage'])) {
                $sql .= " AND c.mileage <= ?";
                $params[] = $sanitizedFilters['maxMileage'];
            }
        }
    }
    
    // Filter by transmission (array support)
    if (!empty($sanitizedFilters['transmission']) && is_array($sanitizedFilters['transmission'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['transmission']) - 1) . '?';
        $sql .= " AND c.transmission IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['transmission']);
    }
    
    // Filter by fuel type (array support)
    if (!empty($sanitizedFilters['fuel_type']) && is_array($sanitizedFilters['fuel_type'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['fuel_type']) - 1) . '?';
        $sql .= " AND c.fuel_type IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['fuel_type']);
    }
    
    // Filter by color (array support)
    if (!empty($sanitizedFilters['color']) && is_array($sanitizedFilters['color'])) {
        $placeholders = str_repeat('?,', count($sanitizedFilters['color']) - 1) . '?';
        $sql .= " AND c.color IN ($placeholders)";
        $params = array_merge($params, $sanitizedFilters['color']);
    }
    
    $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";
    
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log("Search SQL: " . $sql);
        error_log("Search params: " . json_encode($params));
    }
    
    // Execute query
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Params: " . json_encode($params));
        throw $e;
    }
    
    // Format results and apply smart text search matching (fuzzy matching + normalization)
    $results = [];
    
    // Helper function to normalize strings (remove spaces, dashes, convert to lowercase)
    $normalize = function($str) {
        return strtolower(preg_replace('/[\s-]/', '', $str));
    };
    
    // Helper function for fuzzy matching (allows 1-character typo - edit distance of 1)
    $isFuzzyMatch = function($a, $b) {
        if ($a === $b) return true;
        
        $lenA = strlen($a);
        $lenB = strlen($b);
        
        // If length difference is more than 1, can't match with 1 typo
        if (abs($lenA - $lenB) > 1) return false;
        
        // Same length: check for 1 character substitution
        if ($lenA === $lenB) {
            $diff = 0;
            for ($i = 0; $i < $lenA; $i++) {
                if ($a[$i] !== $b[$i]) {
                    $diff++;
                    if ($diff > 1) return false;
                }
            }
            return $diff === 1;
        }
        
        // Different length by 1: check for insertion/deletion
        // $a is longer, $b is shorter (or vice versa)
        $longer = $lenA > $lenB ? $a : $b;
        $shorter = $lenA > $lenB ? $b : $a;
        $lenLong = strlen($longer);
        $lenShort = strlen($shorter);
        
        // Check if removing one character from longer gives shorter
        for ($i = 0; $i < $lenLong; $i++) {
            $test = substr($longer, 0, $i) . substr($longer, $i + 1);
            if ($test === $shorter) return true;
        }
        
        return false;
    };
    
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
            'color' => $row['color'] ?? null
        ];
        
        // Apply smart text search matching if query exists
        if ($searchQuery) {
            $normMake = $normalize($car['make']);
            $normModel = $normalize($car['model']);
            $normSearch = $normalize($searchQuery);
            
            // Check if make matches (startsWith OR fuzzy match - check both directions)
            $matchesMake = (strpos($normMake, $normSearch) === 0) || 
                          (strpos($normSearch, $normMake) === 0) ||
                          $isFuzzyMatch($normMake, $normSearch);
            
            // Check if model matches (startsWith OR fuzzy match - check both directions)
            $matchesModel = (strpos($normModel, $normSearch) === 0) || 
                           (strpos($normSearch, $normModel) === 0) ||
                           $isFuzzyMatch($normModel, $normSearch);
            
            // Check if year matches (contains as string)
            $matchesYear = strpos((string)$car['year'], $searchQuery) !== false;
            
            // Only include car if it matches at least one criteria
            if (!($matchesMake || $matchesModel || $matchesYear)) {
                continue; // Skip this car
            }
        }
        
        $results[] = $car;
    }
    
    // Log search metadata (optional)
    try {
        $metaStmt = $pdo->prepare("
            INSERT INTO search_metadata (search_query, filters, results_count, user_ip)
            VALUES (?, ?, ?, ?)
        ");
        $metaStmt->execute([
            $query,
            json_encode($sanitizedFilters),
            count($results),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        // Don't fail if metadata logging fails
        error_log("Failed to log search metadata: " . $e->getMessage());
    }
    
    // Return XML response
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>true</success>' . "\n";
    $xml .= '  <count>' . count($results) . '</count>' . "\n";
    $xml .= '  <cars>' . "\n";
    
    foreach ($results as $car) {
        $xml .= '    <car>' . "\n";
        $xml .= '      <id>' . htmlspecialchars($car['id'], ENT_XML1, 'UTF-8') . '</id>' . "\n";
        $xml .= '      <make>' . htmlspecialchars($car['make'], ENT_XML1, 'UTF-8') . '</make>' . "\n";
        $xml .= '      <model>' . htmlspecialchars($car['model'], ENT_XML1, 'UTF-8') . '</model>' . "\n";
        $xml .= '      <year>' . (int)$car['year'] . '</year>' . "\n";
        $xml .= '      <price>' . number_format((float)$car['price'], 2, '.', '') . '</price>' . "\n";
        $xml .= '      <mileage>' . (int)$car['mileage'] . '</mileage>' . "\n";
        $xml .= '      <color>' . htmlspecialchars($car['color'] ?? '', ENT_XML1, 'UTF-8') . '</color>' . "\n";
        $xml .= '      <fuel>' . htmlspecialchars($car['fuel_type'] ?? '', ENT_XML1, 'UTF-8') . '</fuel>' . "\n";
        $xml .= '      <transmission>' . htmlspecialchars($car['transmission'] ?? '', ENT_XML1, 'UTF-8') . '</transmission>' . "\n";
        $xml .= '      <vehicle_type>' . htmlspecialchars($car['vehicle_type'] ?? 'car', ENT_XML1, 'UTF-8') . '</vehicle_type>' . "\n";
        if (!empty($car['created_at'])) {
            $xml .= '      <created_at>' . htmlspecialchars($car['created_at'], ENT_XML1, 'UTF-8') . '</created_at>' . "\n";
        }
        
        // Add images
        if (!empty($car['images']) && is_array($car['images'])) {
            foreach ($car['images'] as $image) {
                if ($image) {
                    $xml .= '      <image>' . htmlspecialchars($image, ENT_XML1, 'UTF-8') . '</image>' . "\n";
                }
            }
        }
        
        $xml .= '    </car>' . "\n";
    }
    
    $xml .= '  </cars>' . "\n";
    $xml .= '</response>';
    
    echo trim($xml);
    
} catch (Exception $e) {
    error_log("Search API error: " . $e->getMessage());
    http_response_code(500);
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>false</success>' . "\n";
    $xml .= '  <error>' . htmlspecialchars(DEBUG_MODE ? $e->getMessage() : 'An error occurred while searching', ENT_XML1, 'UTF-8') . '</error>' . "\n";
    $xml .= '</response>';
    
    echo trim($xml);
}
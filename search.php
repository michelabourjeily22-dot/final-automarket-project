<?php
session_start();
require_once 'config.php';

$cars = getAllCars();
// Reverse to show newest first
$cars = array_reverse($cars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Search and filter car listings by make, model, year, and price on AutoFeed.">
    <title>Search Auto - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <!-- Modular CSS -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/search-page.css">
</head>
<body>
    <!-- Search Container -->
    <div class="search-page-container">
        <!-- Header with Back Button -->
        <header class="search-header">
            <button class="back-btn" onclick="window.location.href='index.php'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <h1>Search Auto</h1>
            <div style="width: 40px;"></div> <!-- Spacer for centering -->
        </header>

        <!-- Search Content -->
        <div class="search-content">
            <div class="search-input-wrapper">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" id="searchInput" placeholder="Search by make, model, or year" autocomplete="off">
            </div>
            
            <div class="search-filters">
                <!-- Vehicle Type Filter -->
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="vehicleTypeTrigger">
                        <span class="multi-select-text">All Types</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="vehicleTypeDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="vehicleTypeAll" class="multi-select-checkbox">
                            <label for="vehicleTypeAll">All Types</label>
                        </div>
                        <div class="multi-select-option" data-value="car">
                            <input type="checkbox" id="vehicleType_car" class="multi-select-checkbox" value="car">
                            <label for="vehicleType_car">Car</label>
                        </div>
                        <div class="multi-select-option" data-value="truck">
                            <input type="checkbox" id="vehicleType_truck" class="multi-select-checkbox" value="truck">
                            <label for="vehicleType_truck">Truck</label>
                        </div>
                        <div class="multi-select-option" data-value="motorcycle">
                            <input type="checkbox" id="vehicleType_motorcycle" class="multi-select-checkbox" value="motorcycle">
                            <label for="vehicleType_motorcycle">Motorcycle</label>
                        </div>
                    </div>
                </div>
                
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="makeTrigger">
                        <span class="multi-select-text">All Makes</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="makeDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="makeAll" class="multi-select-checkbox">
                            <label for="makeAll">All Makes</label>
                        </div>
                        <!-- Options will be populated by JavaScript from commonData -->
                    </div>
                </div>
                
                <!-- Model Filter -->
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="modelTrigger">
                        <span class="multi-select-text">Model</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="modelDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="modelAll" class="multi-select-checkbox">
                            <label for="modelAll">All Models</label>
                        </div>
                        <!-- Options will be populated by JavaScript from commonData.modelsByMake -->
                    </div>
                </div>
                
                <!-- Transmission Type Filter -->
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="transmissionTrigger">
                        <span class="multi-select-text">All Transmissions</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="transmissionDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="transmissionAll" class="multi-select-checkbox">
                            <label for="transmissionAll">All Transmissions</label>
                        </div>
                        <div class="multi-select-option" data-value="Automatic">
                            <input type="checkbox" id="transmissionAutomatic" class="multi-select-checkbox" value="Automatic">
                            <label for="transmissionAutomatic">Automatic</label>
                        </div>
                        <div class="multi-select-option" data-value="Manual">
                            <input type="checkbox" id="transmissionManual" class="multi-select-checkbox" value="Manual">
                            <label for="transmissionManual">Manual</label>
                        </div>
                        <div class="multi-select-option" data-value="Steptronic">
                            <input type="checkbox" id="transmissionSteptronic" class="multi-select-checkbox" value="Steptronic">
                            <label for="transmissionSteptronic">Steptronic</label>
                        </div>
                    </div>
                </div>
                
                <!-- Fuel Type Filter -->
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="fuelTypeTrigger">
                        <span class="multi-select-text">All Fuel Types</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="fuelTypeDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="fuelTypeAll" class="multi-select-checkbox">
                            <label for="fuelTypeAll">All Fuel Types</label>
                        </div>
                        <div class="multi-select-option" data-value="Benzine">
                            <input type="checkbox" id="fuelTypeBenzine" class="multi-select-checkbox" value="Benzine">
                            <label for="fuelTypeBenzine">Benzine</label>
                        </div>
                        <div class="multi-select-option" data-value="Diesel">
                            <input type="checkbox" id="fuelTypeDiesel" class="multi-select-checkbox" value="Diesel">
                            <label for="fuelTypeDiesel">Diesel</label>
                        </div>
                        <div class="multi-select-option" data-value="Electric">
                            <input type="checkbox" id="fuelTypeElectric" class="multi-select-checkbox" value="Electric">
                            <label for="fuelTypeElectric">Electric</label>
                        </div>
                        <div class="multi-select-option" data-value="Hybrid">
                            <input type="checkbox" id="fuelTypeHybrid" class="multi-select-checkbox" value="Hybrid">
                            <label for="fuelTypeHybrid">Hybrid</label>
                        </div>
                    </div>
                </div>
                
                <!-- Color Filter -->
                <div class="multi-select-wrapper">
                    <div class="multi-select-trigger" id="colorTrigger">
                        <span class="multi-select-text">All Colors</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="multi-select-dropdown" id="colorDropdown">
                        <div class="multi-select-option select-all-option" data-value="all">
                            <input type="checkbox" id="colorAll" class="multi-select-checkbox">
                            <label for="colorAll">All Colors</label>
                        </div>
                        <!-- Options will be populated by JavaScript from commonData -->
                    </div>
                </div>
                
                <!-- Year Range -->
                <div class="range-selector-wrapper">
                    <div class="multi-select-trigger" id="yearRangeTrigger">
                        <span class="multi-select-text">Year Range</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="range-selector-dropdown" id="yearRangeDropdown">
                        <div class="range-inputs">
                            <div class="range-input-group">
                                <label for="minYear">Min Year</label>
                                <input type="number" id="minYear" placeholder="Min" max="2026" step="1">
                            </div>
                            <div class="range-input-group">
                                <label for="maxYear">Max Year</label>
                                <input type="number" id="maxYear" placeholder="Max" max="2026" step="1">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Price Range -->
                <div class="range-selector-wrapper">
                    <div class="multi-select-trigger" id="priceRangeTrigger">
                        <span class="multi-select-text">Price Range</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="range-selector-dropdown" id="priceRangeDropdown">
                        <div class="range-inputs">
                            <div class="range-input-group">
                                <label for="minPrice">Min Price ($)</label>
                                <input type="number" id="minPrice" placeholder="Min" min="0" step="1000">
                            </div>
                            <div class="range-input-group">
                                <label for="maxPrice">Max Price ($)</label>
                                <input type="number" id="maxPrice" placeholder="Max" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mileage Range -->
                <div class="range-selector-wrapper">
                    <div class="multi-select-trigger" id="mileageRangeTrigger">
                        <span class="multi-select-text">Mileage Range</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="range-selector-dropdown" id="mileageRangeDropdown">
                        <div class="range-inputs">
                            <div class="range-input-group">
                                <label for="minMileage">Min Mileage</label>
                                <input type="number" id="minMileage" placeholder="Min" min="0" step="5000">
                            </div>
                            <div class="range-input-group">
                                <label for="maxMileage">Max Mileage</label>
                                <input type="number" id="maxMileage" placeholder="Max" min="0" step="5000">
                            </div>
                        </div>
                        <div class="mileage-unit-selector">
                            <label>Unit:</label>
                            <div class="mileage-unit-buttons">
                                <button type="button" class="mileage-unit-btn active" data-unit="miles" id="mileageUnitMiles">Miles</button>
                                <button type="button" class="mileage-unit-btn" data-unit="km" id="mileageUnitKm">Kilometers (km)</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="searchResults" class="search-results-page"></div>
        </div>
    </div>

    <!-- Modular JavaScript -->
    <script src="js/utils.js"></script>
    <script src="js/multiselect.js"></script>
    <script src="js/search-page.js"></script>
    <script src="js/filter-clear.js"></script>
    <script src="js/main.js"></script>
    
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>


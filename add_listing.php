<?php
session_start();
require_once 'config.php';

$errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session data after retrieving
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Post a new car listing on Auto Marketplace. List your vehicle for sale with photos and details.">
    <title>Post a Car - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <!-- Modular CSS -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/buttons.css">
    <link rel="stylesheet" href="css/modal.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🚗 Auto Marketplace</h1>
            <nav>
                <a href="index.php">Browse Listings</a>
                <a href="add_listing.php" class="active">Post a Car</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="form-section">
            <h2>Post a New Car Listing</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message" style="background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #ff4444;">
                    <strong style="display: block; margin-bottom: 8px;">Please fix the following errors:</strong>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="process_listing.php" method="POST" enctype="multipart/form-data" id="listingForm">
                <div class="form-group">
                    <label for="make">Make <span class="required">*</span></label>
                    <input type="text" id="make" name="make" required 
                           value="<?php echo isset($formData['make']) ? htmlspecialchars($formData['make']) : ''; ?>"
                           placeholder="e.g., Toyota, Honda, Ford">
                </div>

                <div class="form-group">
                    <label for="model">Model <span class="required">*</span></label>
                    <input type="text" id="model" name="model" required
                           value="<?php echo isset($formData['model']) ? htmlspecialchars($formData['model']) : ''; ?>"
                           placeholder="e.g., Camry, Civic, Mustang">
                </div>

                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type <span class="required">*</span></label>
                    <!-- Hidden native select for form submission -->
                    <select id="vehicle_type" name="vehicle_type" required style="display: none;">
                        <option value="">Select Vehicle Type</option>
                        <option value="car" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'car') ? 'selected' : ''; ?>>Car</option>
                        <option value="motorcycle" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                        <option value="truck" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'truck') ? 'selected' : ''; ?>>Truck</option>
                    </select>
                    <!-- Custom dropdown UI -->
                    <div class="multi-select-wrapper">
                        <div class="multi-select-trigger" id="vehicleTypeTrigger">
                            <span class="multi-select-text"><?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type']) ? ucfirst($formData['vehicle_type'] === 'motorcycle' ? 'Motorcycle' : $formData['vehicle_type']) : 'Select Vehicle Type'; ?></span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <div class="multi-select-dropdown" id="vehicleTypeDropdown">
                            <div class="multi-select-option" data-value="car">
                                <input type="radio" name="vehicleType" id="vehicleTypeCar" class="multi-select-radio" value="car" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'car') ? 'checked' : ''; ?>>
                                <label for="vehicleTypeCar">Car</label>
                            </div>
                            <div class="multi-select-option" data-value="motorcycle">
                                <input type="radio" name="vehicleType" id="vehicleTypeMotorcycle" class="multi-select-radio" value="motorcycle" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'motorcycle') ? 'checked' : ''; ?>>
                                <label for="vehicleTypeMotorcycle">Motorcycle</label>
                            </div>
                            <div class="multi-select-option" data-value="truck">
                                <input type="radio" name="vehicleType" id="vehicleTypeTruck" class="multi-select-radio" value="truck" <?php echo (isset($formData['vehicle_type']) && $formData['vehicle_type'] === 'truck') ? 'checked' : ''; ?>>
                                <label for="vehicleTypeTruck">Truck</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="transmission">Transmission <span class="required">*</span></label>
                        <!-- Hidden native select for form submission -->
                        <select id="transmission" name="transmission" required style="display: none;">
                            <option value="">Select Transmission</option>
                            <option value="Manual" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Manual') ? 'selected' : ''; ?>>Manual</option>
                            <option value="Automatic" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
                            <option value="Steptronic" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Steptronic') ? 'selected' : ''; ?>>Steptronic</option>
                        </select>
                        <!-- Custom dropdown UI -->
                        <div class="multi-select-wrapper">
                            <div class="multi-select-trigger" id="transmissionTrigger">
                                <span class="multi-select-text"><?php echo (isset($formData['transmission']) && $formData['transmission']) ? htmlspecialchars($formData['transmission']) : 'Select Transmission'; ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            <div class="multi-select-dropdown" id="transmissionDropdown">
                                <div class="multi-select-option" data-value="Manual">
                                    <input type="radio" name="transmissionRadio" id="transmissionManual" class="multi-select-radio" value="Manual" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Manual') ? 'checked' : ''; ?>>
                                    <label for="transmissionManual">Manual</label>
                                </div>
                                <div class="multi-select-option" data-value="Automatic">
                                    <input type="radio" name="transmissionRadio" id="transmissionAutomatic" class="multi-select-radio" value="Automatic" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Automatic') ? 'checked' : ''; ?>>
                                    <label for="transmissionAutomatic">Automatic</label>
                                </div>
                                <div class="multi-select-option" data-value="Steptronic">
                                    <input type="radio" name="transmissionRadio" id="transmissionSteptronic" class="multi-select-radio" value="Steptronic" <?php echo (isset($formData['transmission']) && $formData['transmission'] === 'Steptronic') ? 'checked' : ''; ?>>
                                    <label for="transmissionSteptronic">Steptronic</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fuel_type">Fuel Type <span class="required">*</span></label>
                        <!-- Hidden native select for form submission -->
                        <select id="fuel_type" name="fuel_type" required style="display: none;">
                            <option value="">Select Fuel Type</option>
                            <option value="Diesel" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                            <option value="Electric" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Electric') ? 'selected' : ''; ?>>Electric</option>
                            <option value="Benzine" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Benzine') ? 'selected' : ''; ?>>Benzine</option>
                            <option value="Hybrid" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                        </select>
                        <!-- Custom dropdown UI -->
                        <div class="multi-select-wrapper">
                            <div class="multi-select-trigger" id="fuelTypeTrigger">
                                <span class="multi-select-text"><?php echo (isset($formData['fuel_type']) && $formData['fuel_type']) ? htmlspecialchars($formData['fuel_type']) : 'Select Fuel Type'; ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            <div class="multi-select-dropdown" id="fuelTypeDropdown">
                                <div class="multi-select-option" data-value="Diesel">
                                    <input type="radio" name="fuelTypeRadio" id="fuelTypeDiesel" class="multi-select-radio" value="Diesel" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Diesel') ? 'checked' : ''; ?>>
                                    <label for="fuelTypeDiesel">Diesel</label>
                                </div>
                                <div class="multi-select-option" data-value="Electric">
                                    <input type="radio" name="fuelTypeRadio" id="fuelTypeElectric" class="multi-select-radio" value="Electric" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Electric') ? 'checked' : ''; ?>>
                                    <label for="fuelTypeElectric">Electric</label>
                                </div>
                                <div class="multi-select-option" data-value="Benzine">
                                    <input type="radio" name="fuelTypeRadio" id="fuelTypeBenzine" class="multi-select-radio" value="Benzine" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Benzine') ? 'checked' : ''; ?>>
                                    <label for="fuelTypeBenzine">Benzine</label>
                                </div>
                                <div class="multi-select-option" data-value="Hybrid">
                                    <input type="radio" name="fuelTypeRadio" id="fuelTypeHybrid" class="multi-select-radio" value="Hybrid" <?php echo (isset($formData['fuel_type']) && $formData['fuel_type'] === 'Hybrid') ? 'checked' : ''; ?>>
                                    <label for="fuelTypeHybrid">Hybrid</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Year <span class="required">*</span></label>
                        <input type="number" id="year" name="year" required min="1900" 
                               max="<?php echo date('Y') + 1; ?>"
                               value="<?php echo isset($formData['year']) ? htmlspecialchars($formData['year']) : ''; ?>"
                               placeholder="e.g., 2020">
                    </div>

                    <div class="form-group">
                        <label for="mileage">Mileage <span class="required">*</span></label>
                        <input type="number" id="mileage" name="mileage" required min="0"
                               value="<?php echo isset($formData['mileage']) ? htmlspecialchars($formData['mileage']) : ''; ?>"
                               placeholder="e.g., 25000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="color">Color <span class="required">*</span></label>
                        <input type="text" id="color" name="color" required
                               value="<?php echo isset($formData['color']) ? htmlspecialchars($formData['color']) : ''; ?>"
                               placeholder="e.g., Red, Blue, Silver">
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" required min="0" step="0.01"
                               value="<?php echo isset($formData['price']) ? htmlspecialchars($formData['price']) : ''; ?>"
                               placeholder="e.g., 25000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="images">Images <span class="required">*</span></label>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple required>
                    <small>You can select multiple images. Maximum file size: 5MB per image.</small>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Post Listing</button>
                    <button type="reset" class="btn-secondary" id="resetForm">Clear Form</button>
                    <a href="index.php" class="btn-secondary" style="text-decoration: none; display: inline-block;">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="js/form.js"></script>
    <script src="js/form-dropdowns.js"></script>
    <script src="js/main.js"></script>
</body>
</html>


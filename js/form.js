// Form Validation and Image Preview

/**
 * Initialize custom dropdowns for modal form selects
 * Replaces native selects with custom dropdowns that match search page styling
 */
function initModalFormDropdowns() {
    // Vehicle Type dropdown
    initModalSingleSelectDropdown('modalVehicleTypeTrigger', 'modalVehicleTypeDropdown', 'vehicle_type', 'Select Vehicle Type');
    
    // Transmission dropdown
    initModalSingleSelectDropdown('modalTransmissionTrigger', 'modalTransmissionDropdown', 'transmission', 'Select Transmission');
    
    // Fuel Type dropdown
    initModalSingleSelectDropdown('modalFuelTypeTrigger', 'modalFuelTypeDropdown', 'fuel_type', 'Select Fuel Type');
}

/**
 * Initialize a single-select dropdown for modal form
 * @param {string} triggerId - ID of the trigger element
 * @param {string} dropdownId - ID of the dropdown container
 * @param {string} selectId - ID of the hidden native select element
 * @param {string} defaultText - Default text to display
 */
function initModalSingleSelectDropdown(triggerId, dropdownId, selectId, defaultText) {
    const trigger = document.getElementById(triggerId);
    const dropdown = document.getElementById(dropdownId);
    const nativeSelect = document.getElementById(selectId);
    
    if (!trigger || !dropdown || !nativeSelect) {
        return;
    }
    
    const textElement = trigger.querySelector('.multi-select-text');
    const radioButtons = dropdown.querySelectorAll('.multi-select-radio');
    
    // Set initial text based on native select value
    const initialValue = nativeSelect.value;
    if (textElement) {
        if (initialValue) {
            const selectedOption = nativeSelect.querySelector(`option[value="${initialValue}"]`);
            textElement.textContent = selectedOption ? selectedOption.textContent : defaultText;
            trigger.style.color = 'var(--text-primary)';
        } else {
            textElement.textContent = defaultText;
            trigger.style.color = 'var(--text-secondary)';
        }
    }
    
    // Toggle dropdown on trigger click
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isActive = dropdown.classList.contains('active');
        
        // Close all other dropdowns before opening this one
        if (typeof closeAllDropdowns === 'function') {
            closeAllDropdowns(dropdown);
        } else {
            // Fallback if closeAllDropdowns not available
            document.querySelectorAll('.multi-select-dropdown').forEach(dd => {
                if (dd !== dropdown) {
                    dd.classList.remove('active');
                    const prevTrigger = dd.previousElementSibling;
                    if (prevTrigger && prevTrigger.classList.contains('multi-select-trigger')) {
                        prevTrigger.classList.remove('active');
                    }
                }
            });
        }
        
        if (!isActive) {
            // Get trigger position relative to viewport
            const triggerRect = trigger.getBoundingClientRect();
            
            // Calculate exact width to match trigger (round to avoid sub-pixel issues)
            const exactWidth = Math.round(triggerRect.width);
            
            // Set width properties to match trigger exactly
            dropdown.style.setProperty('width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('min-width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('max-width', exactWidth + 'px', 'important');
            
            // Calculate reasonable dropdown height based on content
            const optionCount = radioButtons.length;
            const optionHeight = 44; // Height per option (padding + content)
            const dropdownPadding = 8; // Top/bottom padding of dropdown
            const calculatedHeight = (optionCount * optionHeight) + (dropdownPadding * 2);
            const maxDropdownHeight = Math.min(calculatedHeight, 200); // Max 200px for compact dropdown
            dropdown.style.maxHeight = maxDropdownHeight + 'px';
            
            // Position directly below the trigger, perfectly aligned to left edge
            // For position: fixed, use viewport coordinates directly (getBoundingClientRect already gives viewport coords)
            let topPosition = Math.round(triggerRect.bottom + 4);
            const leftPosition = Math.round(triggerRect.left);
            
            // Check if dropdown would go off screen, position above if needed
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            const requiredSpace = maxDropdownHeight + 8; // 4px gap + some buffer
            
            if (spaceBelow < requiredSpace && spaceAbove > spaceBelow) {
                topPosition = Math.round(triggerRect.top - maxDropdownHeight - 4);
            }
            
            // Set position with important to override any CSS
            dropdown.style.setProperty('top', topPosition + 'px', 'important');
            dropdown.style.setProperty('left', leftPosition + 'px', 'important');
            
            // Ensure dropdown doesn't go off screen horizontally
            const spaceRight = window.innerWidth - triggerRect.right;
            if (spaceRight < 0) {
                dropdown.style.setProperty('left', Math.round(window.innerWidth - exactWidth - 4) + 'px', 'important');
            }
            if (triggerRect.left < 4) {
                dropdown.style.setProperty('left', '4px', 'important');
            }
            
            // Show the dropdown
            dropdown.classList.add('active');
            trigger.classList.add('active');
        } else {
            // Close dropdown
            dropdown.classList.remove('active');
            trigger.classList.remove('active');
        }
    });
    
    // Handle radio button selection
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedValue = this.value;
            
            // Update native select
            nativeSelect.value = selectedValue;
            
            // Update trigger text
            const label = this.closest('.multi-select-option').querySelector('label');
            if (textElement && label) {
                textElement.textContent = label.textContent.trim();
                trigger.style.color = 'var(--text-primary)';
            }
            
            // Close dropdown
            dropdown.classList.remove('active');
            trigger.classList.remove('active');
            
            // Trigger change event on native select for validation
            const changeEvent = new Event('change', { bubbles: true });
            nativeSelect.dispatchEvent(changeEvent);
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            trigger.classList.remove('active');
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}

// Initialize modal form dropdowns when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalFormDropdowns);
} else {
    initModalFormDropdowns();
}

// Re-initialize when modal opens (for dynamically loaded content)
document.addEventListener('click', function(e) {
    if (e.target && (e.target.id === 'postBtn' || e.target.closest('#postBtn'))) {
        setTimeout(initModalFormDropdowns, 100);
    }
});

// Make functions globally available
window.initModalFormDropdowns = initModalFormDropdowns;
window.initModalSingleSelectDropdown = initModalSingleSelectDropdown;

/**
 * Initialize image preview functionality
 */
function initImagePreview() {
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('imagePreview');
    
    if (!imageInput || !previewContainer) return;
    
    imageInput.addEventListener('change', function(e) {
        previewContainer.innerHTML = '';
        const files = Array.from(e.target.files);
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview ' + (index + 1);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '×';
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', 'Remove image');
                    
                    removeBtn.addEventListener('click', function() {
                        previewItem.remove();
                        removeFileFromInput(file.name);
                    });
                    
                    previewItem.appendChild(img);
                    previewItem.appendChild(removeBtn);
                    previewContainer.appendChild(previewItem);
                };
                
                reader.readAsDataURL(file);
            }
        });
    });
}

/**
 * Remove file from input
 * @param {string} fileName - Name of file to remove
 */
function removeFileFromInput(fileName) {
    const input = document.getElementById('images');
    if (!input) return;
    
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach(file => {
        if (file.name !== fileName) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    
    // Trigger change event to update preview
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

/**
 * Validate Post Car form - comprehensive validation with inline errors
 */
function validatePostForm() {
    const form = document.getElementById('listingForm');
    if (!form) return false;
    
    // Clear all previous errors
    clearAllErrors();
    
    let isValid = true;
    let firstErrorElement = null;
    
    // Validate Vehicle Type (check hidden select value)
    const vehicleType = document.getElementById('vehicle_type');
    const vehicleTypeTrigger = document.getElementById('vehicleTypeTrigger');
    if (vehicleType && (!vehicleType.value || vehicleType.value.trim() === '')) {
        showFieldError(vehicleTypeTrigger, vehicleType, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = vehicleTypeTrigger || vehicleType;
        isValid = false;
    }
    
    // Validate Make (check hidden select value)
    const make = document.getElementById('make');
    const makeTrigger = document.getElementById('makeTrigger');
    if (make && (!make.value || make.value.trim() === '')) {
        showFieldError(makeTrigger, make, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = makeTrigger || make;
        isValid = false;
    }
    
    // Validate Model (check hidden select value)
    const model = document.getElementById('model');
    const modelTrigger = document.getElementById('modelTrigger');
    if (model && (!model.value || model.value.trim() === '')) {
        showFieldError(modelTrigger, model, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = modelTrigger || model;
        isValid = false;
    }
    
    // Validate Year
    const year = document.getElementById('year');
    if (year && (!year.value || year.value.trim() === '')) {
        showFieldError(null, year, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = year;
        isValid = false;
    }
    
    // Validate Mileage
    const mileage = document.getElementById('mileage');
    if (mileage && (!mileage.value || mileage.value.trim() === '')) {
        showFieldError(null, mileage, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = mileage;
        isValid = false;
    }
    
    // Validate Transmission (check hidden select value)
    const transmission = document.getElementById('transmission');
    const transmissionTrigger = document.getElementById('transmissionTrigger');
    if (transmission && (!transmission.value || transmission.value.trim() === '')) {
        showFieldError(transmissionTrigger, transmission, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = transmissionTrigger || transmission;
        isValid = false;
    }
    
    // Validate Fuel Type (check hidden select value)
    const fuelType = document.getElementById('fuel_type');
    const fuelTypeTrigger = document.getElementById('fuelTypeTrigger');
    if (fuelType && (!fuelType.value || fuelType.value.trim() === '')) {
        showFieldError(fuelTypeTrigger, fuelType, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = fuelTypeTrigger || fuelType;
        isValid = false;
    }
    
    // Validate Color (check hidden select value)
    const color = document.getElementById('color');
    const colorTrigger = document.getElementById('colorTrigger');
    if (color && (!color.value || color.value.trim() === '')) {
        showFieldError(colorTrigger, color, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = colorTrigger || color;
        isValid = false;
    }
    
    // Validate Price
    const price = document.getElementById('price');
    if (price && (!price.value || price.value.trim() === '')) {
        showFieldError(null, price, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = price;
        isValid = false;
    }
    
    // Validate Images
    const images = document.getElementById('images');
    if (images && (!images.files || images.files.length === 0)) {
        showFieldError(null, images, 'Please select at least one image');
        if (!firstErrorElement) firstErrorElement = images;
        isValid = false;
    }
    
    // Validate Seller Phone
    const sellerPhone = document.getElementById('seller_phone');
    if (sellerPhone && (!sellerPhone.value || sellerPhone.value.trim() === '')) {
        showFieldError(null, sellerPhone, 'Please fill out this field');
        if (!firstErrorElement) firstErrorElement = sellerPhone;
        isValid = false;
    }
    
    // Scroll to first error if any
    if (!isValid && firstErrorElement) {
        firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Small delay to ensure scroll completes before focusing
        setTimeout(function() {
            if (firstErrorElement.focus) {
                firstErrorElement.focus();
            }
        }, 300);
    }
    
    return isValid;
}

/**
 * Show error on a field
 * @param {HTMLElement|null} trigger - Custom dropdown trigger (if dropdown field)
 * @param {HTMLElement} field - Hidden select or input element
 * @param {string} errorMessage - Error message to display
 */
function showFieldError(trigger, field, errorMessage) {
    if (trigger) {
        // For dropdown fields, highlight the trigger
        trigger.classList.add('input-error');
        
        // Update trigger text to show error
        const textElement = trigger.querySelector('.multi-select-text');
        if (textElement) {
            textElement.textContent = errorMessage;
            textElement.style.color = '#ff3b3b';
        }
        
        // Add error message element after the wrapper if it doesn't exist
        const wrapper = trigger.closest('.multi-select-wrapper');
        if (wrapper) {
            let errorMsg = wrapper.querySelector('.error-msg');
            if (!errorMsg) {
                errorMsg = document.createElement('span');
                errorMsg.className = 'error-msg';
                errorMsg.textContent = 'Required';
                wrapper.appendChild(errorMsg);
            }
        }
    }
    
    if (field) {
        // For regular input fields, highlight the input
        field.classList.add('input-error');
        
        // Update placeholder for text/number inputs
        if (field.tagName === 'INPUT' && (field.type === 'text' || field.type === 'number')) {
            field.placeholder = errorMessage;
        }
        
        // Add error message element after the input if it doesn't exist
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            let errorMsg = formGroup.querySelector('.error-msg');
            if (!errorMsg) {
                errorMsg = document.createElement('span');
                errorMsg.className = 'error-msg';
                errorMsg.textContent = 'Required';
                formGroup.appendChild(errorMsg);
            }
        }
    }
}

/**
 * Clear all error states from form
 */
function clearAllErrors() {
    // Remove error classes from all inputs and triggers
    document.querySelectorAll('.input-error').forEach(el => {
        el.classList.remove('input-error');
    });
    
    // Remove error messages
    document.querySelectorAll('.error-msg').forEach(el => {
        el.remove();
    });
    
    // Restore normal text color on dropdown triggers
    document.querySelectorAll('.multi-select-text').forEach(textEl => {
        if (textEl.style.color === 'rgb(255, 59, 59)' || textEl.style.color === '#ff3b3b') {
            textEl.style.color = '';
        }
    });
    
    // Restore normal placeholders for text/number inputs
    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
        if (input.placeholder === 'Please fill out this field' || input.placeholder === 'Please select at least one image') {
            // Restore original placeholder or clear it
            const originalPlaceholder = input.getAttribute('data-original-placeholder') || '';
            input.placeholder = originalPlaceholder;
        }
    });
}

/**
 * Initialize form validation and error clearing
 */
function initFormValidation() {
    const form = document.getElementById('listingForm');
    if (!form) return;
    
    // Store original placeholders
    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
        if (input.placeholder && !input.getAttribute('data-original-placeholder')) {
            input.setAttribute('data-original-placeholder', input.placeholder);
        }
    });
    
    // Form submit handler
    form.addEventListener('submit', function(e) {
        if (!validatePostForm()) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        
        // Form is valid - show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';
        }
    });
    
    // Clear errors when user interacts with fields
    setupErrorClearing();
}

/**
 * Setup event listeners to clear errors when fields are filled
 */
function setupErrorClearing() {
    const form = document.getElementById('listingForm');
    if (!form) return;
    
    // Clear errors on text/number input changes
    form.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            clearFieldError(input);
        });
        
        input.addEventListener('change', function() {
            if (this.value && this.value.trim() !== '') {
                clearFieldError(this);
            }
        });
    });
    
    // Clear errors on file input changes
    const imagesInput = document.getElementById('images');
    if (imagesInput) {
        imagesInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                clearFieldError(this);
            }
        });
    }
    
    // Clear errors on dropdown selections (check hidden select changes)
    const dropdownSelects = ['vehicle_type', 'make', 'model', 'transmission', 'fuel_type', 'color'];
    dropdownSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', function() {
                if (this.value && this.value.trim() !== '') {
                    clearFieldError(this);
                    // Also clear trigger error
                    let triggerId = selectId.replace('_', '') + 'Trigger';
                    if (selectId === 'vehicle_type') triggerId = 'vehicleTypeTrigger';
                    if (selectId === 'fuel_type') triggerId = 'fuelTypeTrigger';
                    const trigger = document.getElementById(triggerId);
                    if (trigger) {
                        clearFieldError(trigger);
                    }
                }
            });
        }
    });
}

/**
 * Clear error for a specific field
 * @param {HTMLElement} field - Input or trigger element
 */
function clearFieldError(field) {
    if (!field) return;
    
    // Remove error class
    field.classList.remove('input-error');
    
    // Clear error message
    const formGroup = field.closest('.form-group');
    const wrapper = field.closest('.multi-select-wrapper');
    const container = formGroup || wrapper;
    
    if (container) {
        const errorMsg = container.querySelector('.error-msg');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
    
    // Restore normal text color on dropdown trigger text
    if (field.classList.contains('multi-select-trigger')) {
        const textElement = field.querySelector('.multi-select-text');
        if (textElement) {
            // If the text is an error message, restore it from the hidden select value
            if (textElement.textContent === 'Please fill out this field') {
                const selectIdMap = {
                    'vehicleTypeTrigger': 'vehicle_type',
                    'makeTrigger': 'make',
                    'modelTrigger': 'model',
                    'transmissionTrigger': 'transmission',
                    'fuelTypeTrigger': 'fuel_type',
                    'colorTrigger': 'color'
                };
                const selectId = selectIdMap[field.id];
                if (selectId) {
                    const select = document.getElementById(selectId);
                    if (select && select.value) {
                        const selectedOption = select.querySelector(`option[value="${select.value}"]`);
                        if (selectedOption) {
                            textElement.textContent = selectedOption.textContent.trim();
                        } else {
                            // Fallback to default text
                            const defaultTextMap = {
                                'vehicleTypeTrigger': 'Select Vehicle Type',
                                'makeTrigger': 'Select Make',
                                'modelTrigger': 'Select Model',
                                'transmissionTrigger': 'Select Transmission',
                                'fuelTypeTrigger': 'Select Fuel Type',
                                'colorTrigger': 'Select Color'
                            };
                            textElement.textContent = defaultTextMap[field.id] || '';
                        }
                    } else {
                        // Restore default text if no value selected
                        const defaultTextMap = {
                            'vehicleTypeTrigger': 'Select Vehicle Type',
                            'makeTrigger': 'Select Make',
                            'modelTrigger': 'Select Model',
                            'transmissionTrigger': 'Select Transmission',
                            'fuelTypeTrigger': 'Select Fuel Type',
                            'colorTrigger': 'Select Color'
                        };
                        textElement.textContent = defaultTextMap[field.id] || '';
                    }
                }
            }
            textElement.style.color = '';
        }
    }
    
    // Restore original placeholder for inputs
    if (field.tagName === 'INPUT' && (field.type === 'text' || field.type === 'number')) {
        const originalPlaceholder = field.getAttribute('data-original-placeholder') || '';
        if (field.placeholder === 'Please fill out this field' || field.placeholder === 'Please select at least one image') {
            field.placeholder = originalPlaceholder;
        }
    }
}

// Make validation functions globally available
window.validatePostForm = validatePostForm;
window.clearFieldError = clearFieldError;
window.clearAllErrors = clearAllErrors;


/**
 * Initialize custom dropdowns for Add Post form page
 * Replaces native selects with custom dropdowns that match search page styling
 */

/**
 * Common data structure for Make, Model, and Color dropdowns
 * Expanded to include Car, Motorcycle, and Truck data
 */
const commonData = {
    // Car makes and models (full list)
    makes: [
        "Toyota", "Honda", "Nissan", "Mazda", "Subaru", "Mitsubishi",
        "Hyundai", "Kia", "Genesis",
        "BMW", "Mercedes-Benz", "Audi", "Volkswagen", "Porsche",
        "Ford", "Chevrolet", "Dodge", "Jeep", "GMC", "Cadillac",
        "Lexus", "Infiniti", "Acura",
        "Volvo", "Land Rover", "Range Rover", "Jaguar",
        "Peugeot", "Renault", "Citroen", "Fiat", "Alfa Romeo",
        "Suzuki", "Isuzu",
        "Tesla", "Mini", "Bentley", "Rolls Royce",
        "Ferrari", "Lamborghini", "Aston Martin", "Maserati",
        "Bugatti", "McLaren",
        "Chrysler", "Buick", "Lincoln",
        "Skoda", "Seat", "Opel",
        "Hummer", "Saab", "Pontiac"
    ],
    
    modelsByMake: {
        "Toyota": [
            "Corolla", "Camry", "Yaris", "Avalon", "Supra", "86",
            "RAV4", "Highlander", "4Runner", "Prado", "Land Cruiser",
            "Hilux", "Tacoma", "Tundra", "C-HR", "Fortuner",
            "FJ Cruiser"
        ],
        "Honda": ["Civic", "Accord", "City", "Jazz", "CR-V", "HR-V", "Pilot", "Odyssey", "Fit", "Insight", "Legend"],
        "Nissan": ["Altima", "Sentra", "Sunny", "Maxima", "370Z", "GT-R", "X-Trail", "Patrol", "Armada", "Juke", "Murano", "Titan", "Qashqai"],
        "Mazda": ["Mazda 2", "Mazda 3", "Mazda 6", "CX-3", "CX-5", "CX-9", "MX-5"],
        "Subaru": ["Impreza", "Legacy", "Outback", "Forester", "XV", "BRZ", "WRX"],
        "Mitsubishi": ["Lancer", "Evo X", "Mirage", "Outlander", "Pajero", "ASX"],
        "Hyundai": ["Elantra", "Sonata", "Accent", "Veloster", "Tucson", "Santa Fe", "Palisade", "Kona"],
        "Kia": ["Rio", "Cerato", "Optima", "Stinger", "Sportage", "Sorento", "Telluride", "Seltos"],
        "Genesis": ["G70", "G80", "G90", "GV70", "GV80"],
        "BMW": ["116i", "118i", "218i", "316i", "320i", "330i", "520i", "530i", "740i", "M3", "M5", "M4", "X1", "X3", "X5", "X6"],
        "Mercedes-Benz": ["A200", "C180", "C200", "C300", "E200", "E300", "S350", "S450", "AMG GT", "GLA", "GLC", "GLE", "GLS"],
        "Audi": ["A1", "A3", "A4", "A5", "A6", "A7", "Q2", "Q3", "Q5", "Q7", "S3", "RS3", "RS6"],
        "Volkswagen": ["Golf", "Passat", "Polo", "Tiguan", "Touareg", "Jetta", "Scirocco"],
        "Porsche": ["911", "Panamera", "Cayenne", "Macan", "Taycan"],
        "Ford": ["Fiesta", "Focus", "Fusion", "Mustang", "Explorer", "Expedition", "F-150"],
        "Chevrolet": ["Spark", "Cruze", "Malibu", "Camaro", "Corvette", "Tahoe", "Suburban", "Silverado"],
        "Dodge": ["Charger", "Challenger", "Durango", "Ram"],
        "Jeep": ["Wrangler", "Cherokee", "Grand Cherokee", "Compass", "Renegade"],
        "GMC": ["Terrain", "Acadia", "Yukon", "Sierra"],
        "Cadillac": ["ATS", "CTS", "Escalade", "XT5"],
        "Lexus": ["IS250", "IS300", "ES350", "GS350", "RX350", "LX570", "NX200t"],
        "Infiniti": ["Q50", "Q60", "QX50", "QX60", "QX80"],
        "Acura": ["ILX", "TLX", "RDX", "MDX", "NSX"],
        "Volvo": ["S60", "S90", "XC40", "XC60", "XC90"],
        "Land Rover": ["Discovery", "Defender", "Freelander"],
        "Range Rover": ["Evoque", "Velar", "Sport", "Autobiography"],
        "Jaguar": ["XE", "XF", "XJ", "F-Type", "E-Pace", "F-Pace"],
        "Peugeot": ["208", "301", "308", "508", "3008", "5008"],
        "Renault": ["Clio", "Megane", "Fluence", "Talisman", "Kadjar", "Koleos"],
        "Citroen": ["C3", "C4", "C5", "Aircross"],
        "Fiat": ["500", "Punto", "Tipo", "Abarth", "Doblo"],
        "Alfa Romeo": ["Giulia", "Stelvio", "Giulietta"],
        "Suzuki": ["Swift", "Vitara", "Jimny", "Ertiga"],
        "Isuzu": ["D-MAX", "MU-X"],
        "Tesla": ["Model S", "Model 3", "Model X", "Model Y", "Cybertruck"],
        "Mini": ["Cooper", "Countryman", "Paceman"],
        "Bentley": ["Continental GT", "Flying Spur", "Bentayga"],
        "Rolls Royce": ["Ghost", "Phantom", "Wraith", "Cullinan"],
        "Ferrari": ["488", "812 Superfast", "Roma", "Portofino"],
        "Lamborghini": ["Aventador", "Huracan", "Urus"],
        "Aston Martin": ["DB9", "DB11", "Vantage"],
        "Maserati": ["Ghibli", "Quattroporte", "Levante"],
        "McLaren": ["570S", "720S", "Artura"],
        "Chrysler": ["300C", "Pacifica"],
        "Buick": ["Encore", "Envision", "Enclave"],
        "Lincoln": ["MKZ", "Corsair", "Aviator", "Navigator"],
        "Skoda": ["Octavia", "Superb", "Kodiaq"],
        "Seat": ["Ibiza", "Leon", "Ateca"],
        "Opel": ["Corsa", "Astra", "Insignia"],
        "Hummer": ["H1", "H2", "H3"],
        "Saab": ["9-3", "9-5"],
        "Pontiac": ["G6", "G8", "Firebird"]
    },
    
    // Motorcycle makes and models
    motorcycleMakes: [
        "Honda", "Yamaha", "Kawasaki", "Suzuki",
        "Harley-Davidson", "Ducati", "KTM", "Triumph", "BMW Motorrad"
    ],
    
    motorcycleModels: {
        "Honda": ["CBR600RR", "CB500F", "Africa Twin", "CBR1000RR"],
        "Yamaha": ["R1", "R6", "MT-07", "MT-09"],
        "Kawasaki": ["Ninja 400", "Ninja 650", "Z900", "Z1000"],
        "Suzuki": ["GSX-R600", "GSX-R750", "Hayabusa"],
        "Harley-Davidson": ["Street 750", "Iron 883", "Fat Boy"],
        "Ducati": ["Panigale V4", "Monster 821", "Streetfighter V4"],
        "KTM": ["Duke 390", "RC 390", "1290 Super Duke"],
        "Triumph": ["Street Triple", "Bonneville", "Speed Triple"],
        "BMW Motorrad": ["S1000RR", "GS1250", "F900R"]
    },
    
    // Truck makes and models
    truckMakes: ["Ford", "Chevrolet", "GMC", "Ram", "Toyota", "Nissan", "Jeep", "Mercedes"],
    
    truckModels: {
        "Ford": ["F-150", "F-250", "Ranger", "Raptor"],
        "Chevrolet": ["Silverado 1500", "Colorado", "Silverado 2500"],
        "GMC": ["Sierra 1500", "Sierra 2500", "Canyon"],
        "Ram": ["1500", "2500", "TRX"],
        "Toyota": ["Hilux", "Tundra", "Tacoma"],
        "Nissan": ["Frontier", "Titan"],
        "Jeep": ["Gladiator"],
        "Mercedes": ["X-Class"]
    },
    
    colors: ["White", "Black", "Grey", "Silver", "Blue", "Red"]
};

/**
 * Initialize a single-select dropdown for form page
 * @param {string} triggerId - ID of the trigger element
 * @param {string} dropdownId - ID of the dropdown container
 * @param {string} selectId - ID of the hidden native select element
 * @param {string} defaultText - Default text to display
 */
function initFormSingleSelectDropdown(triggerId, dropdownId, selectId, defaultText) {
    const trigger = document.getElementById(triggerId);
    const dropdown = document.getElementById(dropdownId);
    const nativeSelect = document.getElementById(selectId);
    
    if (!trigger || !dropdown || !nativeSelect) {
        return;
    }
    
    // Mark as initialized to prevent double initialization
    if (trigger.hasAttribute('data-initialized')) {
        return;
    }
    trigger.setAttribute('data-initialized', 'true');
    
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
    
    // Also check if a radio button is already checked (for form data restoration)
    const checkedRadio = dropdown.querySelector('.multi-select-radio:checked');
    if (checkedRadio && textElement) {
        const label = checkedRadio.closest('.multi-select-option').querySelector('label');
        if (label) {
            textElement.textContent = label.textContent.trim();
            trigger.style.color = 'var(--text-primary)';
        }
    }
    
    // Ensure trigger is clickable and visible
    trigger.style.pointerEvents = 'auto';
    trigger.style.cursor = 'pointer';
    trigger.style.position = 'relative';
    trigger.style.zIndex = '1';
    
    // Ensure dropdown is properly positioned and visible
    dropdown.style.position = 'fixed';
    dropdown.style.zIndex = '99999';
    dropdown.style.overflow = 'visible';
    
    // Remove any existing click handlers by storing reference
    let clickHandler = null;
    
    // Toggle dropdown on trigger click
    clickHandler = function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        
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
            const maxDropdownHeight = Math.min(calculatedHeight, 200); // Max 200px to match CSS
            dropdown.style.maxHeight = maxDropdownHeight + 'px';
            dropdown.style.minHeight = 'auto';
            
            // Position directly below the trigger, perfectly aligned to left edge
            let topPosition = Math.round(triggerRect.bottom + 4);
            const leftPosition = Math.round(triggerRect.left);
            
            // Check if dropdown would go off screen, position above if needed
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            const requiredSpace = 200 + 8; // Max height 200px + 4px gap + some buffer
            
            if (spaceBelow < requiredSpace && spaceAbove > spaceBelow) {
                topPosition = Math.round(triggerRect.top - 200 - 4);
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
    };
    
    // Attach the event listener
    trigger.addEventListener('click', clickHandler, true);
    
    // Handle radio button selection
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedValue = this.value;
            
            // Update native select
            nativeSelect.value = selectedValue;
            
            // Update trigger text immediately
            const label = this.closest('.multi-select-option').querySelector('label');
            if (textElement && label) {
                textElement.textContent = label.textContent.trim();
                trigger.style.color = 'var(--text-primary)';
            }
            
            // Clear any validation errors when value is selected
            if (typeof clearFieldError === 'function') {
                clearFieldError(trigger);
                clearFieldError(nativeSelect);
            }
            
            // Close dropdown immediately (most important for Make dropdown)
            dropdown.classList.remove('active');
            trigger.classList.remove('active');
            
            // Trigger change event on native select for validation
            const changeEvent = new Event('change', { bubbles: true });
            nativeSelect.dispatchEvent(changeEvent);
            
            // Special handling for Make dropdown - update Model dropdown
            if (selectId === 'make') {
                setTimeout(function() {
                    updateModelDropdown(selectedValue);
                }, 10);
            }
            
            // Special handling for Vehicle Type dropdown - repopulate Make dropdown
            if (selectId === 'vehicle_type') {
                // Clear selected make and model
                const makeSelect = document.getElementById('make');
                const modelSelect = document.getElementById('model');
                if (makeSelect) {
                    makeSelect.value = '';
                    const makeTrigger = document.getElementById('makeTrigger');
                    if (makeTrigger) {
                        const makeTextElement = makeTrigger.querySelector('.multi-select-text');
                        if (makeTextElement) {
                            makeTextElement.textContent = 'Select Make';
                            makeTrigger.style.color = 'var(--text-secondary)';
                        }
                    }
                }
                if (modelSelect) {
                    modelSelect.value = '';
                    const modelTrigger = document.getElementById('modelTrigger');
                    if (modelTrigger) {
                        const modelTextElement = modelTrigger.querySelector('.multi-select-text');
                        if (modelTextElement) {
                            modelTextElement.textContent = 'Select Make First';
                            modelTrigger.style.color = 'var(--text-secondary)';
                        }
                    }
                }
                
                // Repopulate Make dropdown with filtered makes based on vehicle type
                setTimeout(function() {
                    populatePostCarMakeDropdown();
                    // Re-initialize Make dropdown after repopulating
                    const makeTriggerEl = document.getElementById('makeTrigger');
                    if (makeTriggerEl) {
                        if (makeTriggerEl.hasAttribute('data-initialized')) {
                            makeTriggerEl.removeAttribute('data-initialized');
                        }
                        initFormSingleSelectDropdown('makeTrigger', 'makeDropdown', 'make', 'Select Make');
                    }
                }, 50);
            }
        });
        
        // Also ensure that clicking on the radio itself triggers immediate closing
        radio.addEventListener('click', function(e) {
            // When radio is clicked, ensure dropdown closes after change event
            // The change event handler above will handle closing, but we ensure it happens
            setTimeout(function() {
                if (this.checked) {
                    dropdown.classList.remove('active');
                    trigger.classList.remove('active');
                }
            }.bind(this), 0);
        });
        
        // Also handle clicking on the entire option - ensure dropdown closes immediately
        const option = radio.closest('.multi-select-option');
        option.addEventListener('click', function(e) {
            // If clicking directly on radio or label, let native behavior handle it
            // But we'll close the dropdown in the change handler above
            if (e.target === radio || e.target.tagName === 'LABEL') {
                // Native behavior will handle the change event, which will close dropdown
                return;
            }
            // If clicking elsewhere on the option, trigger the radio and let change event close dropdown
            e.preventDefault();
            e.stopPropagation();
            radio.checked = true;
            // Trigger change event which will handle closing and updating
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
    
    // Close dropdown when clicking outside (use capture phase for better reliability)
    const outsideClickHandler = function(e) {
        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            trigger.classList.remove('active');
        }
    };
    
    // Use capture phase and attach to document
    document.addEventListener('click', outsideClickHandler, true);
    
    // Prevent dropdown from closing when clicking inside
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
    }, true);
    
    // Prevent dropdown from closing when scrolling inside it
    dropdown.addEventListener('scroll', function(e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
    }, true);
    
    // Reposition dropdown on scroll and resize
    function repositionDropdown() {
        if (dropdown.classList.contains('active')) {
            const triggerRect = trigger.getBoundingClientRect();
            const exactWidth = Math.round(triggerRect.width);
            
            dropdown.style.setProperty('width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('min-width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('max-width', exactWidth + 'px', 'important');
            
            let topPosition = Math.round(triggerRect.bottom + 4);
            const leftPosition = Math.round(triggerRect.left);
            
            const optionCount = radioButtons.length;
            const optionHeight = 44;
            const dropdownPadding = 8;
            const calculatedHeight = (optionCount * optionHeight) + (dropdownPadding * 2);
            const maxDropdownHeight = Math.min(calculatedHeight, 200);
            
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            const requiredSpace = maxDropdownHeight + 8;
            
            if (spaceBelow < requiredSpace && spaceAbove > spaceBelow) {
                topPosition = Math.round(triggerRect.top - maxDropdownHeight - 4);
            }
            
            dropdown.style.setProperty('top', topPosition + 'px', 'important');
            dropdown.style.setProperty('left', leftPosition + 'px', 'important');
        }
    }
    
    // Close dropdown on window scroll (but not when scrolling inside dropdown)
    window.addEventListener('scroll', function(e) {
        if (dropdown.classList.contains('active')) {
            // Check if scroll happened inside the dropdown
            const scrollTarget = e.target;
            if (scrollTarget && dropdown.contains(scrollTarget)) {
                // Scroll happened inside dropdown, don't close
                return;
            }
            // Scroll happened outside dropdown, close it
            if (typeof closeAllDropdowns === 'function') {
                closeAllDropdowns();
            } else {
                dropdown.classList.remove('active');
                trigger.classList.remove('active');
            }
        }
    }, { passive: true, capture: true });
    
    // Still handle resize for repositioning if needed
    window.addEventListener('resize', function() {
        if (dropdown.classList.contains('active')) {
            // On resize, reposition instead of closing
            const triggerRect = trigger.getBoundingClientRect();
            const exactWidth = Math.round(triggerRect.width);
            dropdown.style.setProperty('width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('min-width', exactWidth + 'px', 'important');
            dropdown.style.setProperty('max-width', exactWidth + 'px', 'important');
            let topPosition = Math.round(triggerRect.bottom + 4);
            const leftPosition = Math.round(triggerRect.left);
            const optionCount = radioButtons.length;
            const optionHeight = 44;
            const dropdownPadding = 8;
            const calculatedHeight = (optionCount * optionHeight) + (dropdownPadding * 2);
            const maxDropdownHeight = Math.min(calculatedHeight, 200);
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            const requiredSpace = maxDropdownHeight + 8;
            if (spaceBelow < requiredSpace && spaceAbove > spaceBelow) {
                topPosition = Math.round(triggerRect.top - maxDropdownHeight - 4);
            }
            dropdown.style.setProperty('top', topPosition + 'px', 'important');
            dropdown.style.setProperty('left', leftPosition + 'px', 'important');
            dropdown.style.setProperty('position', 'fixed', 'important');
        }
    });
}

/**
 * Populate Make dropdown from commonData (for Post Car page)
 * Filters makes based on selected vehicle type
 */
function populatePostCarMakeDropdown() {
    const makeSelect = document.getElementById('make');
    const makeDropdown = document.getElementById('makeDropdown');
    const makeTrigger = document.getElementById('makeTrigger');
    
    if (!makeSelect || !makeDropdown || !makeTrigger) {
        return; // Not on Post Car page
    }
    
    // Get selected vehicle type
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const selectedVehicleType = vehicleTypeSelect ? vehicleTypeSelect.value : '';
    
    // Get saved make value if form has errors (check before clearing)
    const savedMakeOption = makeSelect.querySelector('option[selected]');
    const savedMake = savedMakeOption ? savedMakeOption.value : '';
    
    // Clear existing options
    makeSelect.innerHTML = '<option value="">Select Make</option>';
    makeDropdown.innerHTML = '';
    
    // Determine which makes to show based on vehicle type
    let makesToShow = [];
    
    if (!selectedVehicleType || selectedVehicleType === 'car') {
        // Show car makes if no type selected or car is selected
        makesToShow = makesToShow.concat(commonData.makes || []);
    }
    
    if (selectedVehicleType === 'motorcycle') {
        makesToShow = makesToShow.concat(commonData.motorcycleMakes || []);
    }
    
    if (selectedVehicleType === 'truck') {
        makesToShow = makesToShow.concat(commonData.truckMakes || []);
    }
    
    // Remove duplicates and sort
    makesToShow = [...new Set(makesToShow)];
    makesToShow.sort();
    
    if (makesToShow.length === 0) {
        return;
    }
    
    // Add makes to dropdown
    makesToShow.forEach(function(make, index) {
        // Add to hidden select
        const option = document.createElement('option');
        option.value = make;
        option.textContent = make;
        if (savedMake === make) {
            option.selected = true;
        }
        makeSelect.appendChild(option);
        
        // Add to custom dropdown
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', make);
        
        const radioId = 'make' + make.replace(/\s+/g, '') + index;
        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'makeRadio';
        radio.id = radioId;
        radio.className = 'multi-select-radio';
        radio.value = make;
        if (savedMake === make) {
            radio.checked = true;
        }
        
        const label = document.createElement('label');
        label.setAttribute('for', radioId);
        label.textContent = make;
        
        optionDiv.appendChild(radio);
        optionDiv.appendChild(label);
        makeDropdown.appendChild(optionDiv);
    });
    
    // Update trigger text if saved make exists
    if (savedMake) {
        const makeTextElement = makeTrigger.querySelector('.multi-select-text');
        if (makeTextElement) {
            makeTextElement.textContent = savedMake;
            makeTrigger.style.color = 'var(--text-primary)';
        }
    }
}

/**
 * Populate Color dropdown from commonData (for Post Car page)
 */
function populatePostCarColorDropdown() {
    const colorSelect = document.getElementById('color');
    const colorDropdown = document.getElementById('colorDropdown');
    const colorTrigger = document.getElementById('colorTrigger');
    
    if (!colorSelect || !colorDropdown || !colorTrigger) {
        return; // Not on Post Car page
    }
    
    // Clear existing options
    colorSelect.innerHTML = '<option value="">Select Color</option>';
    colorDropdown.innerHTML = '';
    
    // Get saved color value if form has errors
    const savedColor = colorSelect.querySelector('option[selected]') ? colorSelect.querySelector('option[selected]').value : '';
    
    // Add colors from commonData
    commonData.colors.forEach(function(color, index) {
        // Add to hidden select
        const option = document.createElement('option');
        option.value = color;
        option.textContent = color;
        if (savedColor === color) {
            option.selected = true;
        }
        colorSelect.appendChild(option);
        
        // Add to custom dropdown
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', color);
        
        const radioId = 'color' + color.replace(/\s+/g, '') + index;
        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'colorRadio';
        radio.id = radioId;
        radio.className = 'multi-select-radio';
        radio.value = color;
        if (savedColor === color) {
            radio.checked = true;
        }
        
        const label = document.createElement('label');
        label.setAttribute('for', radioId);
        label.textContent = color;
        
        optionDiv.appendChild(radio);
        optionDiv.appendChild(label);
        colorDropdown.appendChild(optionDiv);
    });
    
    // Update trigger text if saved color exists
    if (savedColor) {
        const colorTextElement = colorTrigger.querySelector('.multi-select-text');
        if (colorTextElement) {
            colorTextElement.textContent = savedColor;
            colorTrigger.style.color = 'var(--text-primary)';
        }
    }
}

/**
 * Update Model dropdown based on selected Make (Post Car page only)
 */
function updateModelDropdown(selectedMake) {
    const modelSelect = document.getElementById('model');
    const modelDropdown = document.getElementById('modelDropdown');
    const modelTrigger = document.getElementById('modelTrigger');
    
    if (!modelSelect || !modelDropdown || !modelTrigger) {
        return; // Not on Post Car page
    }
    
    // Get saved model value before clearing (for form error restoration)
    const savedModelOption = modelSelect.querySelector('option[selected]');
    const savedModel = savedModelOption ? savedModelOption.value : '';
    
    // Clear existing options
    modelSelect.innerHTML = '<option value="">Select Model</option>';
    modelDropdown.innerHTML = '';
    
    // Get selected vehicle type
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const selectedVehicleType = vehicleTypeSelect ? vehicleTypeSelect.value : '';
    
    // Reset trigger text
    const modelTextElement = modelTrigger.querySelector('.multi-select-text');
    if (modelTextElement) {
        if (!selectedMake) {
            modelTextElement.textContent = 'Select Make First';
        } else {
            modelTextElement.textContent = 'Select Model';
        }
        modelTrigger.style.color = 'var(--text-secondary)';
    }
    
    // If no make selected, stop here
    if (!selectedMake) {
        return;
    }
    
    // Get models for selected make based on vehicle type
    let models = [];
    
    if (!selectedVehicleType || selectedVehicleType === 'car') {
        // Check car models
        if (commonData.modelsByMake && commonData.modelsByMake[selectedMake]) {
            models = models.concat(commonData.modelsByMake[selectedMake]);
        }
    }
    
    if (selectedVehicleType === 'motorcycle') {
        // Check motorcycle models
        if (commonData.motorcycleModels && commonData.motorcycleModels[selectedMake]) {
            models = models.concat(commonData.motorcycleModels[selectedMake]);
        }
    }
    
    if (selectedVehicleType === 'truck') {
        // Check truck models
        if (commonData.truckModels && commonData.truckModels[selectedMake]) {
            models = models.concat(commonData.truckModels[selectedMake]);
        }
    }
    
    // If no models found, stop here
    if (models.length === 0) {
        if (modelTextElement) {
            modelTextElement.textContent = 'Select Make First';
        }
        return;
    }
    
    // Update hidden select
    models.forEach(function(model) {
        const option = document.createElement('option');
        option.value = model;
        option.textContent = model;
        if (savedModel === model) {
            option.selected = true;
        }
        modelSelect.appendChild(option);
    });
    
    // Update custom dropdown UI
    models.forEach(function(model, index) {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', model);
        
        const radioId = 'model' + model.replace(/\s+/g, '') + index;
        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'modelRadio';
        radio.id = radioId;
        radio.className = 'multi-select-radio';
        radio.value = model;
        if (savedModel === model) {
            radio.checked = true;
        }
        
        const label = document.createElement('label');
        label.setAttribute('for', radioId);
        label.textContent = model;
        
        optionDiv.appendChild(radio);
        optionDiv.appendChild(label);
        modelDropdown.appendChild(optionDiv);
        
        // Update trigger text if saved model exists
        if (savedModel === model && modelTextElement) {
            modelTextElement.textContent = model;
            modelTrigger.style.color = 'var(--text-primary)';
        }
        
        // Add event listeners to new radio buttons
        radio.addEventListener('change', function() {
            modelSelect.value = this.value;
            if (modelTextElement) {
                modelTextElement.textContent = model;
                modelTrigger.style.color = 'var(--text-primary)';
            }
            modelDropdown.classList.remove('active');
            modelTrigger.classList.remove('active');
            const changeEvent = new Event('change', { bubbles: true });
            modelSelect.dispatchEvent(changeEvent);
        });
        
        // Handle clicking on the option
        optionDiv.addEventListener('click', function(e) {
            if (e.target !== radio && e.target.tagName !== 'LABEL') {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Re-initialize the dropdown to ensure proper event handling
    setTimeout(function() {
        // Remove existing initialization flag if present
        if (modelTrigger.hasAttribute('data-initialized')) {
            modelTrigger.removeAttribute('data-initialized');
        }
        // Re-initialize with the new options
        initFormSingleSelectDropdown('modelTrigger', 'modelDropdown', 'model', 'Select Model');
    }, 50);
}

/**
 * Initialize all form dropdowns
 */
function initFormDropdowns() {
    // Use setTimeout to ensure DOM is fully ready
    setTimeout(function() {
        // Vehicle Type dropdown
        initFormSingleSelectDropdown('vehicleTypeTrigger', 'vehicleTypeDropdown', 'vehicle_type', 'Select Vehicle Type');
        
        // Make dropdown (if exists - for add_post.php)
        const makeTrigger = document.getElementById('makeTrigger');
        if (makeTrigger) {
            // Remove any existing initialization flags to allow fresh initialization
            if (makeTrigger.hasAttribute('data-initialized')) {
                makeTrigger.removeAttribute('data-initialized');
            }
            
            // Populate Make dropdown from commonData (creates radio buttons)
            populatePostCarMakeDropdown();
            
            // Initialize the dropdown after populating (this attaches event listeners to radio buttons)
            initFormSingleSelectDropdown('makeTrigger', 'makeDropdown', 'make', 'Select Make');
            
            // Initialize Model dropdown with current Make value (if form has errors)
            const makeSelect = document.getElementById('make');
            if (makeSelect) {
                if (makeSelect.value) {
                    updateModelDropdown(makeSelect.value);
                }
                
                // Also listen to native select change (backup)
                makeSelect.addEventListener('change', function() {
                    updateModelDropdown(this.value);
                });
            }
        }
        
        // Model dropdown (if exists - for add_post.php)
        const modelTrigger = document.getElementById('modelTrigger');
        if (modelTrigger) {
            // Initialize the dropdown (it may have been updated by updateModelDropdown)
            const makeSelect = document.getElementById('make');
            const defaultText = (makeSelect && makeSelect.value) ? 'Select Model' : 'Select Make First';
            initFormSingleSelectDropdown('modelTrigger', 'modelDropdown', 'model', defaultText);
        }
        
        // Color dropdown (if exists - for add_post.php)
        const colorTrigger = document.getElementById('colorTrigger');
        if (colorTrigger) {
            // Remove any existing initialization flags to allow fresh initialization
            if (colorTrigger.hasAttribute('data-initialized')) {
                colorTrigger.removeAttribute('data-initialized');
            }
            
            // Populate Color dropdown from commonData (creates radio buttons)
            populatePostCarColorDropdown();
            
            // Initialize the dropdown after populating (use small delay to ensure DOM is ready)
            // This attaches event listeners to radio buttons for single-select behavior
            setTimeout(function() {
                initFormSingleSelectDropdown('colorTrigger', 'colorDropdown', 'color', 'Select Color');
            }, 10);
        }
        
        // Transmission dropdown
        initFormSingleSelectDropdown('transmissionTrigger', 'transmissionDropdown', 'transmission', 'Select Transmission');
        
        // Fuel Type dropdown
        initFormSingleSelectDropdown('fuelTypeTrigger', 'fuelTypeDropdown', 'fuel_type', 'Select Fuel Type');
    }, 50);
}

// Initialize form dropdowns when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initFormDropdowns, 100);
    });
} else {
    // DOM already loaded, initialize immediately
    setTimeout(initFormDropdowns, 100);
}

// Handle form reset to reset custom dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('listingForm');
    if (form) {
        const resetBtn = document.getElementById('resetForm');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // Reset all custom dropdowns
                setTimeout(function() {
                    const triggers = [
                        'vehicleTypeTrigger', 
                        'makeTrigger', 
                        'modelTrigger', 
                        'transmissionTrigger', 
                        'fuelTypeTrigger',
                        'colorTrigger'
                    ];
                    const defaultTexts = [
                        'Select Vehicle Type', 
                        'Select Make', 
                        'Select Model', 
                        'Select Transmission', 
                        'Select Fuel Type',
                        'Select Color'
                    ];
                    
                    triggers.forEach(function(triggerId, index) {
                        const trigger = document.getElementById(triggerId);
                        if (trigger) {
                            const textElement = trigger.querySelector('.multi-select-text');
                            if (textElement) {
                                textElement.textContent = defaultTexts[index];
                                trigger.style.color = 'var(--text-secondary)';
                            }
                        }
                    });
                }, 0);
            });
        }
    }
});

// Make functions globally available
window.initFormDropdowns = initFormDropdowns;
window.initFormSingleSelectDropdown = initFormSingleSelectDropdown;


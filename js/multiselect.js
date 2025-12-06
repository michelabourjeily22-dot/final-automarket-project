// Multi-Select Dropdown Functionality

/**
 * Common data structure for Make, Model, and Color dropdowns
 */
const commonData = {
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
 * Close all open dropdowns
 * Centralized function to ensure only one dropdown is open at a time
 */
function closeAllDropdowns(excludeDropdown = null) {
    document.querySelectorAll('.multi-select-dropdown, .range-selector-dropdown').forEach(dd => {
        if (dd !== excludeDropdown && dd.classList.contains('active')) {
            dd.classList.remove('active');
            // Remove active class from associated trigger
            const trigger = dd.previousElementSibling;
            if (trigger && trigger.classList.contains('multi-select-trigger')) {
                trigger.classList.remove('active');
            }
            // Also check for triggers that might be siblings or parents
            const wrapper = dd.closest('.multi-select-wrapper, .range-selector-wrapper');
            if (wrapper) {
                const wrapperTrigger = wrapper.querySelector('.multi-select-trigger');
                if (wrapperTrigger) {
                    wrapperTrigger.classList.remove('active');
                }
            }
        }
    });
}

/**
 * Get selected vehicle types from checkboxes
 */
function getSelectedVehicleTypes() {
    const checkboxes = document.querySelectorAll('#vehicleTypeDropdown .multi-select-checkbox:not(#vehicleTypeAll)');
    return Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);
}

/**
 * Populate Make dropdown from commonData (for search page)
 * Filters makes based on selected vehicle types
 */
function populateMakeDropdown() {
    const makeDropdown = document.getElementById('makeDropdown');
    if (!makeDropdown) return;
    
    // Clear existing options except "All Makes"
    const allOption = makeDropdown.querySelector('.select-all-option');
    makeDropdown.innerHTML = '';
    if (allOption) {
        makeDropdown.appendChild(allOption);
    }
    
    // Get selected vehicle types
    const selectedTypes = getSelectedVehicleTypes();
    
    // Determine which makes to show based on vehicle types
    let makesToShow = [];
    
    if (selectedTypes.length === 0 || selectedTypes.includes('car')) {
        // Show car makes if no type selected or car is selected
        makesToShow = makesToShow.concat(commonData.makes || []);
    }
    
    if (selectedTypes.includes('motorcycle')) {
        makesToShow = makesToShow.concat(commonData.motorcycleMakes || []);
    }
    
    if (selectedTypes.includes('truck')) {
        makesToShow = makesToShow.concat(commonData.truckMakes || []);
    }
    
    // Remove duplicates
    makesToShow = [...new Set(makesToShow)];
    
    // Sort alphabetically
    makesToShow.sort();
    
    if (makesToShow.length === 0) {
        return;
    }
    
    // Add makes to dropdown
    makesToShow.forEach(function(make) {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', make);
        
        const checkboxId = 'make_' + make.replace(/\s+/g, '');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = checkboxId;
        checkbox.className = 'multi-select-checkbox';
        checkbox.value = make;
        
        const label = document.createElement('label');
        label.setAttribute('for', checkboxId);
        label.textContent = make;
        
        optionDiv.appendChild(checkbox);
        optionDiv.appendChild(label);
        makeDropdown.appendChild(optionDiv);
    });
    
    // Re-initialize the dropdown to attach event listeners
    const makeTrigger = document.getElementById('makeTrigger');
    if (makeTrigger) {
        initMultiSelectDropdown('makeTrigger', 'makeDropdown', 'makeAll', 'All Makes');
    }
}

/**
 * Populate Color dropdown from commonData (for search page)
 * Uses checkboxes for multi-select
 */
function populateColorDropdown() {
    // FIX: reset colorTrigger so it can reinitialize correctly
    const colorTrigger = document.getElementById('colorTrigger');
    if (colorTrigger) {
        colorTrigger.removeAttribute('data-multiselect-initialized');
        colorTrigger.style.pointerEvents = "auto";
        colorTrigger.style.cursor = "pointer";
    }
    
    const colorDropdown = document.getElementById('colorDropdown');
    if (!colorDropdown) return;
    
    // Clear existing options except "All Colors"
    const allOption = colorDropdown.querySelector('.select-all-option');
    colorDropdown.innerHTML = '';
    if (allOption) {
        colorDropdown.appendChild(allOption);
    }
    
    // Add colors from commonData as checkboxes
    commonData.colors.forEach(function(color) {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', color);
        
        const checkboxId = 'color_' + color.replace(/\s+/g, '');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = checkboxId;
        checkbox.className = 'multi-select-checkbox';
        checkbox.value = color;
        
        const label = document.createElement('label');
        label.setAttribute('for', checkboxId);
        label.textContent = color;
        
        optionDiv.appendChild(checkbox);
        optionDiv.appendChild(label);
        colorDropdown.appendChild(optionDiv);
    });
    
    // Note: Multi-select initialization will be called after this function
}

/**
 * Update Model dropdown based on selected Make (for search page)
 * Uses checkboxes for multi-select like Make dropdown
 * ALWAYS uses commonData.modelsByMake (never from feed posts or API results)
 */
function updateSearchModelDropdown(selectedMakes) {
    const modelDropdown = document.getElementById('modelDropdown');
    const modelTrigger = document.getElementById('modelTrigger');
    
    if (!modelDropdown || !modelTrigger) {
        return; // Model dropdown doesn't exist on search page yet
    }
    
    // NORMALIZE: Ensure selectedMakes is always an array
    // Handle all possible cases: string, null, undefined, array, etc.
    if (!Array.isArray(selectedMakes)) {
        if (selectedMakes && typeof selectedMakes === 'string') {
            selectedMakes = [selectedMakes];
        } else if (selectedMakes === null || selectedMakes === undefined) {
            selectedMakes = [];
        } else {
            // Try to convert to array (handles NodeList, etc.)
            try {
                selectedMakes = Array.from(selectedMakes);
            } catch (e) {
                selectedMakes = [];
            }
        }
    }
    
    // Ensure all values are strings (in case of mixed types)
    selectedMakes = selectedMakes.filter(m => m).map(m => String(m));
    
    // Clear existing options except "All Models"
    const allOption = modelDropdown.querySelector('.select-all-option');
    modelDropdown.innerHTML = '';
    if (allOption) {
        modelDropdown.appendChild(allOption);
    }
    
    // Get the trigger text element
    const modelTextElement = modelTrigger.querySelector('.multi-select-text');
    
    // If no makes selected, show "Model" label
    if (!selectedMakes || selectedMakes.length === 0) {
        if (modelTextElement) {
            modelTextElement.textContent = 'Model';
            modelTrigger.style.color = 'var(--text-secondary)';
        }
        // Re-initialize the dropdown - remove flag to allow re-initialization
        const modelTriggerEl = document.getElementById('modelTrigger');
        if (modelTriggerEl && modelTriggerEl.hasAttribute('data-initialized')) {
            modelTriggerEl.removeAttribute('data-initialized');
        }
        setTimeout(function() {
            initMultiSelectDropdown('modelTrigger', 'modelDropdown', 'modelAll', 'All Models');
        }, 10);
        return;
    }
    
    // Always show "Model" as the label
    if (modelTextElement) {
        modelTextElement.textContent = 'Model';
        modelTrigger.style.color = 'var(--text-secondary)';
    }
    
    // Get selected vehicle types
    const selectedTypes = getSelectedVehicleTypes();
    
    // Get models to display
    let modelsToShow = [];
    
    // Handle both single and multiple makes with unified logic
    // Use correct model lists based on vehicle type (ALWAYS use static list, never from feed posts or API results)
    if (selectedMakes.length >= 1) {
        const allModels = new Set();
        selectedMakes.forEach(function(make) {
            // Ensure make is a string
            const makeStr = String(make).trim();
            
            // Check which model list to use based on vehicle type
            if (selectedTypes.length === 0 || selectedTypes.includes('car')) {
                // Check car models
                if (commonData && commonData.modelsByMake && commonData.modelsByMake[makeStr]) {
                    commonData.modelsByMake[makeStr].forEach(function(model) {
                        allModels.add(model);
                    });
                }
            }
            
            if (selectedTypes.includes('motorcycle')) {
                // Check motorcycle models
                if (commonData && commonData.motorcycleModels && commonData.motorcycleModels[makeStr]) {
                    commonData.motorcycleModels[makeStr].forEach(function(model) {
                        allModels.add(model);
                    });
                }
            }
            
            if (selectedTypes.includes('truck')) {
                // Check truck models
                if (commonData && commonData.truckModels && commonData.truckModels[makeStr]) {
                    commonData.truckModels[makeStr].forEach(function(model) {
                        allModels.add(model);
                    });
                }
            }
        });
        modelsToShow = Array.from(allModels).sort();
    }
    
    // Create checkbox options for each model
    modelsToShow.forEach(function(model) {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'multi-select-option';
        optionDiv.setAttribute('data-value', model);
        
        const checkboxId = 'model_' + model.replace(/\s+/g, '');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = checkboxId;
        checkbox.className = 'multi-select-checkbox';
        checkbox.value = model;
        
        const label = document.createElement('label');
        label.setAttribute('for', checkboxId);
        label.textContent = model;
        
        optionDiv.appendChild(checkbox);
        optionDiv.appendChild(label);
        modelDropdown.appendChild(optionDiv);
    });
    
    // Re-initialize the dropdown as multi-select - remove flag to allow re-initialization
    const modelTriggerEl = document.getElementById('modelTrigger');
    if (modelTriggerEl && modelTriggerEl.hasAttribute('data-initialized')) {
        modelTriggerEl.removeAttribute('data-initialized');
    }
    // Use setTimeout to ensure DOM is updated
    setTimeout(function() {
        initMultiSelectDropdown('modelTrigger', 'modelDropdown', 'modelAll', 'All Models');
    }, 10);
}

/**
 * Initialize multi-select dropdowns
 * Only initializes custom dropdowns on search/filter pages, not on form pages with standard <select> elements
 */
function initMultiSelect() {
    // Check if we're on a form page (has standard select elements) vs search page (has custom dropdowns)
    const isFormPage = document.getElementById('vehicle_type') && 
                       document.getElementById('transmission') && 
                       document.getElementById('fuel_type');
    
    // If we're on a form page with standard selects, skip initialization
    if (isFormPage) {
        return;
    }
    
    // Initialize vehicle type dropdown if it exists (for search page)
    const vehicleTypeTrigger = document.getElementById('vehicleTypeTrigger');
    if (vehicleTypeTrigger) {
        initMultiSelectDropdown('vehicleTypeTrigger', 'vehicleTypeDropdown', 'vehicleTypeAll', 'All Types');
    }
    
    // Populate and initialize make dropdown from commonData (ALWAYS use static list, never from feed posts or API results)
    const makeTrigger = document.getElementById('makeTrigger');
    if (makeTrigger) {
        // Always populate from commonData.makes (not from dynamic data or feed posts)
        populateMakeDropdown();
        // initMultiSelectDropdown will be called and will handle Make change events
    }
    
    // Initialize multi-select dropdowns (transmission, fuel type, color) - only for search page
    const transmissionTrigger = document.getElementById('transmissionTrigger');
    const fuelTypeTrigger = document.getElementById('fuelTypeTrigger');
    const colorTrigger = document.getElementById('colorTrigger');
    
    if (transmissionTrigger) {
        // Remove any existing initialization flag to allow fresh initialization
        if (transmissionTrigger.hasAttribute('data-multiselect-initialized')) {
            transmissionTrigger.removeAttribute('data-multiselect-initialized');
        }
        if (transmissionTrigger.hasAttribute('data-initialized')) {
            transmissionTrigger.removeAttribute('data-initialized');
        }
        initMultiSelectDropdown('transmissionTrigger', 'transmissionDropdown', 'transmissionAll', 'All Transmissions');
    }
    
    if (fuelTypeTrigger) {
        // Remove any existing initialization flag to allow fresh initialization
        if (fuelTypeTrigger.hasAttribute('data-multiselect-initialized')) {
            fuelTypeTrigger.removeAttribute('data-multiselect-initialized');
        }
        if (fuelTypeTrigger.hasAttribute('data-initialized')) {
            fuelTypeTrigger.removeAttribute('data-initialized');
        }
        initMultiSelectDropdown('fuelTypeTrigger', 'fuelTypeDropdown', 'fuelTypeAll', 'All Fuel Types');
    }
    
    if (colorTrigger) {
        // Remove any existing initialization flag to allow fresh initialization
        if (colorTrigger.hasAttribute('data-multiselect-initialized')) {
            colorTrigger.removeAttribute('data-multiselect-initialized');
        }
        if (colorTrigger.hasAttribute('data-initialized')) {
            colorTrigger.removeAttribute('data-initialized');
        }
        populateColorDropdown();
        // FIX: initialize Color dropdown immediately after DOM is populated
        try {
            initMultiSelectDropdown('colorTrigger', 'colorDropdown', 'colorAll', 'All Colors');
        } catch (err) {
            
            const trigger = document.getElementById('colorTrigger');
            const dropdown = document.getElementById('colorDropdown');
            
            if (trigger && dropdown) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle("active");
                    trigger.classList.toggle("active");
                });
                
                dropdown.addEventListener('click', (e) => e.stopPropagation());
            }
        }
    }
    
    // Initialize Model dropdown if it exists (for search page)
    const modelTrigger = document.getElementById('modelTrigger');
    if (modelTrigger) {
        // Initialize with "Model" label
        const modelTextElement = modelTrigger.querySelector('.multi-select-text');
        if (modelTextElement) {
            modelTextElement.textContent = 'Model';
            modelTrigger.style.color = 'var(--text-secondary)';
        }
        updateSearchModelDropdown([]); // Initialize with empty selection
    }
}

/**
 * Initialize a multi-select dropdown
 * @param {string} triggerId - ID of the trigger button
 * @param {string} dropdownId - ID of the dropdown container
 * @param {string} allCheckboxId - ID of the "Select All" checkbox
 * @param {string} allText - Text for "Select All" option
 */
function initMultiSelectDropdown(triggerId, dropdownId, allCheckboxId, allText) {
    const trigger = document.getElementById(triggerId);
    const dropdown = document.getElementById(dropdownId);
    
    if (!trigger || !dropdown) {
        return;
    }
    
    // Prevent double initialization - check if already initialized
    // FIX: Color is the ONLY filter allowed to reinitialize every time
    if (trigger.hasAttribute('data-multiselect-initialized') && triggerId !== 'colorTrigger') {
        return;
    }
    
    // Mark as initialized
    trigger.setAttribute('data-multiselect-initialized', 'true');
    
    const allCheckbox = document.getElementById(allCheckboxId);
    const textElement = trigger.querySelector('.multi-select-text');
    const checkboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
    const selectAllOption = dropdown.querySelector('.select-all-option');

    // Ensure trigger is clickable and has proper cursor
    trigger.style.cursor = 'pointer';
    trigger.style.pointerEvents = 'auto';

    // Toggle dropdown on trigger click
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isActive = dropdown.classList.contains('active');
        
        // Close all other dropdowns before opening this one
        closeAllDropdowns(dropdown);
        
        if (!isActive) {
            // Calculate position for fixed dropdown
            const triggerRect = trigger.getBoundingClientRect();
            dropdown.style.setProperty('position', 'fixed', 'important');
            dropdown.style.setProperty('top', (triggerRect.bottom + 4) + 'px', 'important');
            dropdown.style.setProperty('left', triggerRect.left + 'px', 'important');
            dropdown.style.setProperty('width', triggerRect.width + 'px', 'important');
            dropdown.style.setProperty('z-index', '99999', 'important');
            
            // Check if dropdown would go off screen, position above if needed
            const dropdownHeight = 200; // max-height - match CSS for scrollable dropdowns
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            
            if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
                // Position above trigger
                dropdown.style.setProperty('top', (triggerRect.top - dropdownHeight - 4) + 'px', 'important');
            }
        }
        
        dropdown.classList.toggle('active');
        trigger.classList.toggle('active', dropdown.classList.contains('active'));
    });

    // Handle "Select All" checkbox
    if (allCheckbox && selectAllOption) {
        selectAllOption.addEventListener('click', function(e) {
            e.stopPropagation();
            const isChecked = allCheckbox.checked;
            
            // Query checkboxes dynamically to avoid stale references (like Vehicle Type does)
            const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
            
            // Toggle all checkboxes
            currentCheckboxes.forEach(cb => {
                cb.checked = !isChecked;
            });
            
            allCheckbox.checked = !isChecked;
            updateSelectAllState();
            updateTriggerText();
            triggerSearch();
            
            // Special handling for Make dropdown - update Model dropdown (for search page)
            if (triggerId === 'makeTrigger') {
                // Query checkboxes dynamically each time to avoid stale references
                const selectedMakes = Array.from(currentCheckboxes)
                    .filter(cb => cb.checked && cb.value)
                    .map(cb => cb.value);
                // Ensure it's always an array before passing
                const normalizedMakes = Array.isArray(selectedMakes) ? selectedMakes : (selectedMakes ? [selectedMakes] : []);
                updateSearchModelDropdown(normalizedMakes);
            }
        });
    }

    // Handle individual checkbox clicks using event delegation for dynamically created checkboxes
    // This ensures it works even after dropdown content is refreshed
    dropdown.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('multi-select-checkbox') && e.target.id !== allCheckboxId) {
            updateSelectAllState();
            updateTriggerText();
            triggerSearch();
            
            // Special handling for Make dropdown - update Model dropdown (for search page)
            if (triggerId === 'makeTrigger') {
                // Query checkboxes dynamically each time to avoid stale references
                const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
                const selectedMakes = Array.from(currentCheckboxes)
                    .filter(cb => cb.checked && cb.value)
                    .map(cb => cb.value);
                // Ensure it's always an array before passing
                const normalizedMakes = Array.isArray(selectedMakes) ? selectedMakes : (selectedMakes ? [selectedMakes] : []);
                updateSearchModelDropdown(normalizedMakes);
            }
            
            // Special handling for Vehicle Type dropdown - repopulate Make dropdown
            if (triggerId === 'vehicleTypeTrigger') {
                // Clear selected makes and models
                const makeDropdown = document.getElementById('makeDropdown');
                if (makeDropdown) {
                    const makeCheckboxes = makeDropdown.querySelectorAll('.multi-select-checkbox');
                    makeCheckboxes.forEach(cb => cb.checked = false);
                    const makeTrigger = document.getElementById('makeTrigger');
                    if (makeTrigger) {
                        const makeTextElement = makeTrigger.querySelector('.multi-select-text');
                        if (makeTextElement) {
                            makeTextElement.textContent = 'All Makes';
                            makeTrigger.style.color = 'var(--text-secondary)';
                        }
                    }
                }
                
                // Clear model dropdown
                updateSearchModelDropdown([]);
                
                // Repopulate Make dropdown with filtered makes based on vehicle types
                setTimeout(function() {
                    populateMakeDropdown();
                }, 50);
            }
        }
    });
    
    // Also attach listeners to existing checkboxes for immediate handling
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateTriggerText();
            triggerSearch();
            
            // Special handling for Make dropdown - update Model dropdown (for search page)
            if (triggerId === 'makeTrigger') {
                // Query checkboxes dynamically each time to avoid stale references
                const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
                const selectedMakes = Array.from(currentCheckboxes)
                    .filter(cb => cb.checked && cb.value)
                    .map(cb => cb.value);
                // Ensure it's always an array before passing
                const normalizedMakes = Array.isArray(selectedMakes) ? selectedMakes : (selectedMakes ? [selectedMakes] : []);
                updateSearchModelDropdown(normalizedMakes);
            }
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

    /**
     * Update "Select All" checkbox state - query checkboxes dynamically
     */
    function updateSelectAllState() {
        if (!allCheckbox) return;
        
        // Query checkboxes dynamically to avoid stale references
        const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
        const checkedCount = Array.from(currentCheckboxes).filter(cb => cb.checked).length;
        const totalCount = currentCheckboxes.length;
        
        if (checkedCount === 0) {
            allCheckbox.checked = false;
            allCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            allCheckbox.checked = true;
            allCheckbox.indeterminate = false;
        } else {
            allCheckbox.checked = false;
            allCheckbox.indeterminate = true;
        }
    }

    /**
     * Update trigger text based on selected items - query checkboxes dynamically
     */
    function updateTriggerText() {
        // Query checkboxes dynamically to avoid stale references
        const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
        const checked = Array.from(currentCheckboxes).filter(cb => cb.checked);
        
        if (checked.length === 0) {
            textElement.textContent = allText;
            trigger.style.color = 'var(--text-secondary)';
        } else if (checked.length === currentCheckboxes.length) {
            textElement.textContent = allText;
            trigger.style.color = 'var(--text-primary)';
        } else if (checked.length === 1) {
            textElement.textContent = checked[0].value;
            trigger.style.color = 'var(--text-primary)';
        } else {
            textElement.textContent = `${checked.length} selected`;
            trigger.style.color = 'var(--text-primary)';
        }
    }

    /**
     * Get selected values
     * @returns {Array} Array of selected values
     */
    function getSelectedValues() {
        // Query checkboxes dynamically each time to avoid stale references
        const currentCheckboxes = dropdown.querySelectorAll('.multi-select-checkbox:not(#' + allCheckboxId + ')');
        return Array.from(currentCheckboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }

    // Expose getSelectedValues function for each dropdown
    if (triggerId === 'vehicleTypeTrigger') {
        window.getSelectedVehicleTypes = getSelectedValues;
    } else if (triggerId === 'makeTrigger') {
        window.getSelectedMakes = getSelectedValues;
    } else if (triggerId === 'modelTrigger') {
        window.getSelectedModels = getSelectedValues;
    } else if (triggerId === 'transmissionTrigger') {
        window.getTransmissionFilter = getSelectedValues; // Returns array
    } else if (triggerId === 'fuelTypeTrigger') {
        window.getFueltypeFilter = getSelectedValues; // Returns array
    } else if (triggerId === 'colorTrigger') {
        window.getColorFilter = getSelectedValues; // Returns array
    } else if (triggerId === 'yearTrigger') {
        window.getSelectedYears = getSelectedValues;
    }

    // Initialize trigger text
    updateTriggerText();
}

/**
 * Trigger search function if it exists
 */
function triggerSearch() {
    if (typeof performSearch === 'function') {
        performSearch();
    }
}

/**
 * Initialize single-select dropdown (radio buttons)
 */
function initSingleSelectDropdown(triggerId, dropdownId, defaultText) {
    const trigger = document.getElementById(triggerId);
    const dropdown = document.getElementById(dropdownId);
    
    if (!trigger || !dropdown) {
        return;
    }
    
    // Mark as initialized to prevent double initialization
    trigger.setAttribute('data-initialized', 'true');
    
    const textElement = trigger.querySelector('.multi-select-text');
    const radioButtons = dropdown.querySelectorAll('.multi-select-radio');
    
    // Ensure trigger is clickable
    trigger.style.pointerEvents = 'auto';
    trigger.style.cursor = 'pointer';
    
    // Initialize selectedValue from checked radio button
    let selectedValue = '';
    const checkedRadio = dropdown.querySelector('.multi-select-radio:checked');
    if (checkedRadio) {
        selectedValue = checkedRadio.value || '';
        if (textElement) {
            if (selectedValue === '') {
                textElement.textContent = defaultText;
                trigger.style.color = 'var(--text-secondary)';
            } else {
                const label = checkedRadio.closest('.multi-select-option').querySelector('label');
                if (label) {
                    textElement.textContent = label.textContent.trim();
                    trigger.style.color = 'var(--text-primary)';
                }
            }
        }
    } else {
        // No radio checked, set default and check the "All" option if it exists
        if (textElement) {
            textElement.textContent = defaultText;
            trigger.style.color = 'var(--text-secondary)';
        }
        // Ensure "All" option is checked by default
        const allOption = dropdown.querySelector('.multi-select-radio[value=""]');
        if (allOption) {
            allOption.checked = true;
        }
    }
    
    // Toggle dropdown on trigger click
    // Use a named function so we can remove it if needed
    function handleTriggerClick(e) {
        e.stopPropagation();
        const isActive = dropdown.classList.contains('active');
        
        // Close all other dropdowns before opening this one
        closeAllDropdowns(dropdown);
        
        if (!isActive) {
            // Calculate position for fixed dropdown
            const triggerRect = trigger.getBoundingClientRect();
            dropdown.style.setProperty('position', 'fixed', 'important');
            dropdown.style.setProperty('top', (triggerRect.bottom + 4) + 'px', 'important');
            dropdown.style.setProperty('left', triggerRect.left + 'px', 'important');
            dropdown.style.setProperty('width', triggerRect.width + 'px', 'important');
            dropdown.style.setProperty('z-index', '99999', 'important');
            
            // Check if dropdown would go off screen, position above if needed
            const dropdownHeight = 200; // Match CSS max-height for scrollable dropdowns
            const spaceBelow = window.innerHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            
            if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
                dropdown.style.setProperty('top', (triggerRect.top - dropdownHeight - 4) + 'px', 'important');
            }
        }
        
        dropdown.classList.toggle('active');
        trigger.classList.toggle('active', dropdown.classList.contains('active'));
    }
    
    // Attach the event listener
    trigger.addEventListener('click', handleTriggerClick);
    
    // Handle radio button selection - ensure immediate search trigger
    radioButtons.forEach(radio => {
        // Handle change event on radio button
        radio.addEventListener('change', function() {
            if (this.checked) {
                selectedValue = this.value || '';
                const label = this.closest('.multi-select-option').querySelector('label');
                if (textElement && label) {
                    if (selectedValue === '') {
                        textElement.textContent = defaultText;
                        trigger.style.color = 'var(--text-secondary)';
                    } else {
                        textElement.textContent = label.textContent.trim();
                        trigger.style.color = 'var(--text-primary)';
                    }
                }
                dropdown.classList.remove('active');
                trigger.classList.remove('active');
                
                // Note: Transmission, Fuel Type, and Color use multi-select, not single-select
                // Their getters are set up in initMultiSelectDropdown
                
                // Trigger search immediately - use setTimeout to ensure getter is updated
                setTimeout(() => {
                    if (typeof triggerSearch === 'function') {
                        triggerSearch();
                    } else if (typeof performSearch === 'function') {
                        performSearch();
                    }
                }, 10);
            }
        });
        
        // Also handle clicking on the option (not just the radio) - ensure it triggers change
        const option = radio.closest('.multi-select-option');
        if (option) {
            option.addEventListener('click', function(e) {
                // Don't prevent default if clicking directly on radio or label
                if (e.target === radio || e.target.tagName === 'LABEL') {
                    return; // Let the native change event handle it
                }
                // If clicking elsewhere on the option, trigger the radio
                e.preventDefault();
                e.stopPropagation();
                radio.checked = true;
                // Dispatch change event to trigger all handlers
                const changeEvent = new Event('change', { bubbles: true, cancelable: true });
                radio.dispatchEvent(changeEvent);
            });
        }
        
        // Also handle label clicks to ensure radio is checked
        const label = radio.closest('.multi-select-option')?.querySelector('label[for="' + radio.id + '"]');
        if (label) {
            label.addEventListener('click', function(e) {
                // Ensure radio is checked when label is clicked
                if (!radio.checked) {
                    radio.checked = true;
                    const changeEvent = new Event('change', { bubbles: true, cancelable: true });
                    radio.dispatchEvent(changeEvent);
                }
            });
        }
    });
    
    // Close dropdown when clicking outside - use capture phase (already set up above)
    // No need for duplicate handler here
    
    // Expose getter function with proper naming (always return current value)
    // Use dropdown ID to find the element dynamically to avoid closure issues
    // Note: Transmission, Fuel Type, and Color use multi-select, not single-select
    // Their getters are set up in initMultiSelectDropdown to return arrays
}

// Auto-initialize when DOM is ready (backup in case main.js doesn't load)
// Use setTimeout to ensure all elements are rendered
function autoInitMultiSelect() {
    // Check if we're on a form page (has standard select elements) vs search page (has custom dropdowns)
    const isFormPage = document.getElementById('vehicle_type') && 
                       document.getElementById('transmission') && 
                       document.getElementById('fuel_type');
    
    // If we're on a form page with standard selects, skip initialization
    if (isFormPage) {
        return;
    }
    
    if (typeof initMultiSelect === 'function') {
        // Make dropdown now ALWAYS uses commonData.makes (no need to fetch dynamic data first)
        // Initialize immediately without waiting for any data fetch
        setTimeout(function() {
            initMultiSelect();
            // Double-check that single-select dropdowns were initialized (only for search page)
            const transmissionTrigger = document.getElementById('transmissionTrigger');
            const fuelTypeTrigger = document.getElementById('fuelTypeTrigger');
            const colorTrigger = document.getElementById('colorTrigger');
            
            // Transmission, Fuel Type, and Color use multi-select - retry only if not already initialized
            if (transmissionTrigger && !transmissionTrigger.hasAttribute('data-multiselect-initialized')) {
                initMultiSelectDropdown('transmissionTrigger', 'transmissionDropdown', 'transmissionAll', 'All Transmissions');
            }
            if (fuelTypeTrigger && !fuelTypeTrigger.hasAttribute('data-multiselect-initialized')) {
                initMultiSelectDropdown('fuelTypeTrigger', 'fuelTypeDropdown', 'fuelTypeAll', 'All Fuel Types');
            }
            if (colorTrigger && !colorTrigger.hasAttribute('data-multiselect-initialized')) {
                populateColorDropdown();
                try {
                    initMultiSelectDropdown('colorTrigger', 'colorDropdown', 'colorAll', 'All Colors');
                } catch (err) {
                    
                    const trigger = document.getElementById('colorTrigger');
                    const dropdown = document.getElementById('colorDropdown');
                    
                    if (trigger && dropdown) {
                        trigger.addEventListener('click', (e) => {
                            e.stopPropagation();
                            dropdown.classList.toggle("active");
                            trigger.classList.toggle("active");
                        });
                        
                        dropdown.addEventListener('click', (e) => e.stopPropagation());
                    }
                }
            }
        }, 100);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInitMultiSelect);
} else {
    autoInitMultiSelect();
}

// Make closeAllDropdowns available globally
if (typeof window !== 'undefined') {
    window.closeAllDropdowns = closeAllDropdowns;
}

/**
 * Close all dropdowns on scroll
 * Uses throttling to avoid performance issues
 */
(function() {
    let scrollTimeout = null;
    let isScrolling = false;
    
    function handleScroll(e) {
        // Check if the scroll event originated from inside a dropdown
        const target = e.target;
        const activeDropdown = document.querySelector('.multi-select-dropdown.active, .range-selector-dropdown.active');
        
        if (activeDropdown && activeDropdown.contains(target)) {
            // Scroll is happening inside the dropdown - don't close it
            e.stopPropagation();
            return;
        }
        
        // Clear existing timeout
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        // Set scrolling flag
        if (!isScrolling) {
            isScrolling = true;
            // Close all dropdowns immediately
            closeAllDropdowns();
        }
        
        // Reset scrolling flag after a short delay
        scrollTimeout = setTimeout(function() {
            isScrolling = false;
        }, 150);
    }
    
    // Add scroll listener with passive option for better performance
    // Use capture phase to catch scroll events early
    window.addEventListener('scroll', handleScroll, { passive: true, capture: true });
    
    // Also handle touchmove for mobile devices
    window.addEventListener('touchmove', handleScroll, { passive: true, capture: true });
    
    // Handle scroll on scrollable containers (like modals)
    document.addEventListener('scroll', handleScroll, { passive: true, capture: true });
})();


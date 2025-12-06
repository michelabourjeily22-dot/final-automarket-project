// Search Page Functionality (standalone page)

/**
 * Initialize search functionality for standalone search page
 */
function initSearchPage() {
    const searchInput = document.getElementById('searchInput');
    const minYear = document.getElementById('minYear');
    const maxYear = document.getElementById('maxYear');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const minMileage = document.getElementById('minMileage');
    const maxMileage = document.getElementById('maxMileage');
    const searchResults = document.getElementById('searchResults');
    
    // Get mileage unit from active button or default to 'miles'
    function getMileageUnit() {
        const activeBtn = document.querySelector('.mileage-unit-btn.active');
        return activeBtn ? activeBtn.getAttribute('data-unit') : 'miles';
    }
    
    if (!searchInput || !searchResults) return;

    /**
     * Get selected vehicle types
     */
    function getSelectedVehicleTypes() {
        const checkboxes = document.querySelectorAll('#vehicleTypeDropdown .multi-select-checkbox:not(#vehicleTypeAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    }
    
    /**
     * Get selected makes - query DOM directly like Vehicle Type
     */
    function getSelectedMakes() {
        const checkboxes = document.querySelectorAll('#makeDropdown .multi-select-checkbox:not(#makeAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }
    
    /**
     * Get selected models - query DOM directly like Vehicle Type
     */
    function getSelectedModels() {
        const checkboxes = document.querySelectorAll('#modelDropdown .multi-select-checkbox:not(#modelAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }
    
    /**
     * Get selected transmissions - query DOM directly like Vehicle Type
     */
    function getSelectedTransmissions() {
        const checkboxes = document.querySelectorAll('#transmissionDropdown .multi-select-checkbox:not(#transmissionAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }
    
    /**
     * Get selected fuel types - query DOM directly like Vehicle Type
     */
    function getSelectedFuelTypes() {
        const checkboxes = document.querySelectorAll('#fuelTypeDropdown .multi-select-checkbox:not(#fuelTypeAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }
    
    /**
     * Get selected colors - query DOM directly like Vehicle Type
     */
    function getSelectedColors() {
        const checkboxes = document.querySelectorAll('#colorDropdown .multi-select-checkbox:not(#colorAll)');
        return Array.from(checkboxes)
            .filter(cb => cb.checked && cb.value)
            .map(cb => cb.value);
    }

    /**
     * Perform search and filter
     */
    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        
        // Get selected filters from multi-select - use local getter functions that query DOM directly
        const selectedVehicleTypes = getSelectedVehicleTypes();
        const selectedMakes = getSelectedMakes();
        const selectedModels = getSelectedModels();
        const transmissionFilter = getSelectedTransmissions();
        const fuelTypeFilter = getSelectedFuelTypes();
        const colorFilter = getSelectedColors();
        
        // Get range filters
        const minYearFilter = minYear && minYear.value ? parseInt(minYear.value) : null;
        const maxYearFilter = maxYear && maxYear.value ? parseInt(maxYear.value) : null;
        const minPriceFilter = minPrice && minPrice.value ? parseFloat(minPrice.value) : null;
        const maxPriceFilter = maxPrice && maxPrice.value ? parseFloat(maxPrice.value) : null;
        const minMileageFilter = minMileage && minMileage.value ? parseFloat(minMileage.value) : null;
        const maxMileageFilter = maxMileage && maxMileage.value ? parseFloat(maxMileage.value) : null;
        const mileageUnitValue = getMileageUnit();

        // Always fetch from API (search page doesn't have cars pre-loaded)
        fetchCarsAndSearch(query, selectedVehicleTypes, selectedMakes, selectedModels, minYearFilter, maxYearFilter, minPriceFilter, maxPriceFilter, minMileageFilter, maxMileageFilter, mileageUnitValue, transmissionFilter, fuelTypeFilter, colorFilter);
    }

    /**
     * Initialize range selector dropdown
     */
    function initRangeSelector(triggerId, dropdownId, defaultText) {
        const trigger = document.getElementById(triggerId);
        const dropdown = document.getElementById(dropdownId);
        
        if (!trigger || !dropdown) return;
        
        const textElement = trigger.querySelector('.multi-select-text');
        
        // Update trigger text based on values
        function updateTriggerText() {
            if (triggerId === 'yearRangeTrigger') {
                const min = document.getElementById('minYear')?.value;
                const max = document.getElementById('maxYear')?.value;
                if (min || max) {
                    textElement.textContent = (min || 'Any') + ' - ' + (max || 'Any');
                } else {
                    textElement.textContent = defaultText;
                }
            } else if (triggerId === 'priceRangeTrigger') {
                const min = document.getElementById('minPrice')?.value;
                const max = document.getElementById('maxPrice')?.value;
                if (min || max) {
                    textElement.textContent = '$' + (min || 'Any') + ' - $' + (max || 'Any');
                } else {
                    textElement.textContent = defaultText;
                }
            } else if (triggerId === 'mileageRangeTrigger') {
                const min = document.getElementById('minMileage')?.value;
                const max = document.getElementById('maxMileage')?.value;
                const activeUnitBtn = document.querySelector('.mileage-unit-btn.active');
                const unit = activeUnitBtn ? activeUnitBtn.getAttribute('data-unit') : 'miles';
                const unitLabel = unit === 'km' ? 'km' : 'miles';
                
                if (min || max) {
                    textElement.textContent = (min || 'Any') + ' - ' + (max || 'Any') + ' ' + unitLabel;
                } else {
                    textElement.textContent = defaultText;
                }
            }
        }
        
        // Toggle dropdown
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = dropdown.classList.contains('active');
            
            // Close all other dropdowns before opening this one
            if (typeof closeAllDropdowns === 'function') {
                closeAllDropdowns(dropdown);
            } else {
                // Fallback if closeAllDropdowns not available
                document.querySelectorAll('.range-selector-dropdown, .multi-select-dropdown').forEach(dd => {
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
                const triggerRect = trigger.getBoundingClientRect();
                dropdown.style.setProperty('position', 'fixed', 'important');
                dropdown.style.setProperty('top', (triggerRect.bottom + 4) + 'px', 'important');
                dropdown.style.setProperty('left', triggerRect.left + 'px', 'important');
                dropdown.style.setProperty('width', Math.max(triggerRect.width, 280) + 'px', 'important');
                dropdown.style.setProperty('z-index', '99999', 'important');
                
                const dropdownHeight = 200;
                const spaceBelow = window.innerHeight - triggerRect.bottom;
                const spaceAbove = triggerRect.top;
                
                if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
                    dropdown.style.setProperty('top', (triggerRect.top - dropdownHeight - 4) + 'px', 'important');
                }
            }
            
            dropdown.classList.toggle('active');
            trigger.classList.toggle('active', dropdown.classList.contains('active'));
        });
        
        // Update trigger text when inputs change
        const inputs = dropdown.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                updateTriggerText();
                // Debounce search on input
                clearTimeout(input._searchTimeout);
                input._searchTimeout = setTimeout(() => {
                    if (typeof performSearch === 'function') {
                        performSearch();
                    }
                }, 300);
            });
            input.addEventListener('change', function() {
                updateTriggerText();
                if (typeof performSearch === 'function') {
                    performSearch();
                }
            });
        });
        
        // Handle mileage unit buttons if they exist
        if (triggerId === 'mileageRangeTrigger') {
            const unitButtons = dropdown.querySelectorAll('.mileage-unit-btn');
            const minMileageInput = document.getElementById('minMileage');
            const maxMileageInput = document.getElementById('maxMileage');

            // Find labels - try multiple selectors
            let minLabel = dropdown.querySelector('label[for="minMileage"]');
            if (!minLabel) {
                minLabel = minMileageInput ? minMileageInput.previousElementSibling : null;
            }
            
            let maxLabel = dropdown.querySelector('label[for="maxMileage"]');
            if (!maxLabel) {
                maxLabel = maxMileageInput ? maxMileageInput.previousElementSibling : null;
            }
            
            // Conversion factor: 1 mile = 1.60934 km
            const MILES_TO_KM = 1.60934;
            const KM_TO_MILES = 1 / MILES_TO_KM;
            
            function updateLabels(unit) {
                if (minLabel) {
                    minLabel.textContent = unit === 'km' ? 'Min Kilometers' : 'Min Mileage';
                }
                if (maxLabel) {
                    maxLabel.textContent = unit === 'km' ? 'Max Kilometers' : 'Max Mileage';
                }
            }
            
            function convertValue(value, fromUnit, toUnit) {
                if (!value || isNaN(value)) return '';
                const numValue = parseFloat(value);
                if (fromUnit === toUnit) return numValue;
                
                if (fromUnit === 'miles' && toUnit === 'km') {
                    return Math.round(numValue * MILES_TO_KM);
                } else if (fromUnit === 'km' && toUnit === 'miles') {
                    return Math.round(numValue * KM_TO_MILES);
                }
                return numValue;
            }
            
            unitButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent dropdown from closing
                    
                    const newUnit = this.getAttribute('data-unit');
                    const currentActiveBtn = dropdown.querySelector('.mileage-unit-btn.active');
                    const currentUnit = currentActiveBtn ? currentActiveBtn.getAttribute('data-unit') : 'miles';
                    
                    // Only convert if switching units
                    if (currentUnit !== newUnit && minMileageInput && maxMileageInput) {
                        // Convert min mileage
                        if (minMileageInput.value && minMileageInput.value.trim() !== '') {
                            const convertedMin = convertValue(minMileageInput.value, currentUnit, newUnit);
                            if (convertedMin !== '') {
                                minMileageInput.value = convertedMin;
                        }
                    }

                        // Convert max mileage
                        if (maxMileageInput.value && maxMileageInput.value.trim() !== '') {
                            const convertedMax = convertValue(maxMileageInput.value, currentUnit, newUnit);
                            if (convertedMax !== '') {
                                maxMileageInput.value = convertedMax;
                            }
                        }
                    }
                    
                    // Remove active class from all buttons
                    unitButtons.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update labels
                    updateLabels(newUnit);
                    
                    // Update trigger text
                    updateTriggerText();
                    
                    // Trigger search - use window.performSearch if available, otherwise call directly
                    setTimeout(() => {
                        if (typeof window.performSearch === 'function') {
                            window.performSearch();
                        } else if (typeof performSearch === 'function') {
                            performSearch();
                        }
                    }, 100);
                });
            });
            
            // Initialize labels based on default unit (miles is default)
            const defaultUnit = 'miles';
            updateLabels(defaultUnit);
            
            // Make sure miles button is active by default if no active button
            const activeBtn = dropdown.querySelector('.mileage-unit-btn.active');
            if (!activeBtn) {
                const milesBtn = dropdown.querySelector('.mileage-unit-btn[data-unit="miles"]');
                if (milesBtn) {
                    milesBtn.classList.add('active');
                }
            }
        }
        
        // Close dropdown when clicking outside - use capture phase
        const outsideClickHandler = function(e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
                trigger.classList.remove('active');
            }
        };
        
        document.addEventListener('click', outsideClickHandler, true);
                    }

    /**
     * Format posted date
     */
    function formatPostedDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) {
            return date.toLocaleDateString('en-US', { weekday: 'long' }); // Monday, Tuesday, etc.
        }
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }); // Dec 15, 2024
    }
    
    /**
     * Convert mileage based on unit
     */
    function convertMileage(mileage, unit) {
        if (unit === 'km') {
            return Math.round(mileage * 1.60934).toLocaleString();
        }
        return mileage.toLocaleString();
    }

    /**
     * Fetch cars from API and perform search
     */
    async function fetchCarsAndSearch(query, selectedVehicleTypes, selectedMakes, selectedModels, minYearFilter, maxYearFilter, minPriceFilter, maxPriceFilter, minMileageFilter, maxMileageFilter, mileageUnit, transmissionFilter, fuelTypeFilter, colorFilter) {
        try {
            // Build JSON request body
            const requestData = {};
            if (query) requestData.q = query;
            if (selectedVehicleTypes.length > 0) {
                requestData.vehicleTypes = selectedVehicleTypes;
            }
            if (selectedMakes.length > 0) {
                requestData.makes = selectedMakes;
            }
            if (selectedModels && selectedModels.length > 0) {
                requestData.models = selectedModels;
            }
            if (transmissionFilter && Array.isArray(transmissionFilter) && transmissionFilter.length > 0) {
                requestData.transmission = transmissionFilter;
            }
            if (fuelTypeFilter && Array.isArray(fuelTypeFilter) && fuelTypeFilter.length > 0) {
                requestData.fuel_type = fuelTypeFilter;
            }
            if (colorFilter && Array.isArray(colorFilter) && colorFilter.length > 0) {
                requestData.color = colorFilter;
            }
            if (minYearFilter !== null) requestData.minYear = minYearFilter;
            if (maxYearFilter !== null) requestData.maxYear = maxYearFilter;
            if (minPriceFilter !== null) requestData.minPrice = minPriceFilter;
            if (maxPriceFilter !== null) requestData.maxPrice = maxPriceFilter;
            if (minMileageFilter !== null) requestData.minMileage = minMileageFilter;
            if (maxMileageFilter !== null) requestData.maxMileage = maxMileageFilter;
            requestData.mileageUnit = mileageUnit;
            
            // Send JSON POST request
            const response = await fetch('api/search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Parse XML response
            const xmlText = await response.text();
            const data = parseXMLResponse(xmlText);
            
            if (!data) {
                throw new Error('Failed to parse XML response');
            }
            
            if (data.success && data.results) {
                displaySearchResultsFromAPI(data.results, mileageUnit);
            } else if (data.success && data.cars) {
                displaySearchResultsFromAPI(data.cars, mileageUnit);
            } else if (data.error) {
                searchResults.innerHTML = '<div class="search-result-item" style="text-align: center; color: #ff4444; padding: 40px 20px;">Error: ' + data.error + '</div>';
            } else {
                searchResults.innerHTML = '<div class="search-result-item" style="text-align: center; color: var(--text-secondary); padding: 40px 20px;">No results found</div>';
            }
        } catch (error) {
            searchResults.innerHTML = '<div class="search-result-item" style="text-align: center; color: #ff4444; padding: 40px 20px;">Error loading results: ' + error.message + '</div>';
        }
    }

    /**
     * Display search results from API data
     */
    function displaySearchResultsFromAPI(cars, mileageUnitValue = 'miles') {
        if (!searchResults) return;
        
        searchResults.innerHTML = '';
        
        if (!cars || cars.length === 0) {
            searchResults.innerHTML = '<div class="search-result-item" style="text-align: center; color: var(--text-secondary); padding: 40px 20px; justify-content: center;">No results found</div>';
            return;
        }

        cars.forEach(car => {
            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';
            
            // Create image element
            let imageHtml = '';
            if (car.images && car.images.length > 0 && car.images[0]) {
                imageHtml = `
                    <div class="search-result-image">
                        <img src="${car.images[0]}" alt="${car.make} ${car.model}" 
                             onerror="this.parentElement.innerHTML='<div class=\\'search-result-image-placeholder\\'>No Image</div>'">
                    </div>
                `;
            } else {
                imageHtml = `
                    <div class="search-result-image">
                        <div class="search-result-image-placeholder">No Image</div>
                    </div>
                `;
            }
            
            // Get mileage unit
            const mileageDisplay = convertMileage(parseInt(car.mileage), mileageUnitValue);
            const mileageLabel = mileageUnitValue === 'km' ? 'km' : 'miles';
            
            // Format posted date
            const postedDate = formatPostedDate(car.created_at);
            
            // Include color in description (like listings)
            const description = `${car.year} • ${mileageDisplay} ${mileageLabel} • ${car.color || 'N/A'}`;
            
            // Create content element
            const contentHtml = `
                <div class="search-result-content">
                    <div class="search-result-title">${car.make} ${car.model}</div>
                    <div class="search-result-details">${description}</div>
                    <div class="search-result-price">$${formatNumber(car.price)}</div>
                    ${postedDate ? `<div class="search-result-date">Posted ${postedDate}</div>` : ''}
                </div>
            `;
            
            resultItem.innerHTML = contentHtml + imageHtml;
            
            // Add click handler to navigate to detail page
            resultItem.addEventListener('click', function() {
                if (car.id) {
                    // Save search state before navigating
                    saveSearchState();
                    window.location.href = `car_detail.php?id=${encodeURIComponent(car.id)}&from=search`;
                }
            });
            
            searchResults.appendChild(resultItem);
        });
    }

    /**
     * Display search results
     * @param {Array} cars - Filtered car array
     */
    function displaySearchResults(cars) {
        searchResults.innerHTML = '';

        if (cars.length === 0) {
            searchResults.innerHTML = '<div class="search-result-item" style="text-align: center; color: var(--text-secondary); padding: 40px 20px; justify-content: center;">No results found</div>';
            return;
        }

        cars.forEach(car => {
            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';
            
            // Create image element
            let imageHtml = '';
            if (car.image) {
                imageHtml = `
                    <div class="search-result-image">
                        <img src="${car.image}" alt="${car.title}" 
                             onerror="this.parentElement.innerHTML='<div class=\\'search-result-image-placeholder\\'>No Image</div>'">
                    </div>
                `;
            } else {
                imageHtml = `
                    <div class="search-result-image">
                        <div class="search-result-image-placeholder">No Image</div>
                    </div>
                `;
            }
            
            // Create content element
            const contentHtml = `
                <div class="search-result-content">
                    <div class="search-result-title">${car.title}</div>
                    <div class="search-result-details">${car.description}</div>
                    <div class="search-result-price">$${formatNumber(car.price)}</div>
                </div>
            `;
            
            resultItem.innerHTML = contentHtml + imageHtml;
            
            // Add click handler to navigate to detail page
            resultItem.addEventListener('click', function() {
                if (car.id) {
                    // Save search state before navigating
                    saveSearchState();
                    window.location.href = `car_detail.php?id=${encodeURIComponent(car.id)}&from=search`;
                }
            });
            
            searchResults.appendChild(resultItem);
        });
    }

    // Initialize range selectors (call after function is defined)
    initRangeSelector('yearRangeTrigger', 'yearRangeDropdown', 'Year Range');
    initRangeSelector('priceRangeTrigger', 'priceRangeDropdown', 'Price Range');
    initRangeSelector('mileageRangeTrigger', 'mileageRangeDropdown', 'Mileage Range');

    // Debounce function for search input
    let searchTimeout = null;
    function debouncedSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch();
        }, 300);
    }
    
    // Event listeners - only search when user interacts
    if (searchInput) {
        searchInput.addEventListener('input', debouncedSearch);
    }
    if (minYear) {
        minYear.addEventListener('input', debouncedSearch);
        minYear.addEventListener('change', performSearch);
    }
    if (maxYear) {
        maxYear.addEventListener('input', debouncedSearch);
        maxYear.addEventListener('change', performSearch);
    }
    if (minPrice) {
        minPrice.addEventListener('input', debouncedSearch);
        minPrice.addEventListener('change', performSearch);
    }
    if (maxPrice) {
        maxPrice.addEventListener('input', debouncedSearch);
        maxPrice.addEventListener('change', performSearch);
    }
    if (minMileage) {
        minMileage.addEventListener('input', debouncedSearch);
        minMileage.addEventListener('change', performSearch);
    }
    if (maxMileage) {
        maxMileage.addEventListener('input', debouncedSearch);
        maxMileage.addEventListener('change', performSearch);
    }
    
    // New filters are handled by multiselect.js initSingleSelectDropdown which triggers search automatically

    /**
     * Save current search state to sessionStorage
     */
    function saveSearchState() {
        const state = {
            searchQuery: searchInput ? searchInput.value : '',
            minYear: minYear ? minYear.value : '',
            maxYear: maxYear ? maxYear.value : '',
            minPrice: minPrice ? minPrice.value : '',
            maxPrice: maxPrice ? maxPrice.value : '',
            minMileage: minMileage ? minMileage.value : '',
            maxMileage: maxMileage ? maxMileage.value : '',
            mileageUnit: getMileageUnit(),
            scrollPosition: window.scrollY || window.pageYOffset,
            // Get selected vehicle types
            vehicleTypes: getSelectedVehicleTypes(),
            // Get selected makes
            makes: typeof getSelectedMakes === 'function' ? getSelectedMakes() : [],
            // Get transmission, fuel type, color filters
            transmission: typeof getTransmissionFilter === 'function' ? getTransmissionFilter() : '',
            fuelType: typeof getFueltypeFilter === 'function' ? getFueltypeFilter() : '',
            color: typeof getColorFilter === 'function' ? getColorFilter() : ''
        };
        sessionStorage.setItem('searchPageState', JSON.stringify(state));
    }

    /**
     * Restore search state from sessionStorage
     */
    function restoreSearchState() {
        const stateStr = sessionStorage.getItem('searchPageState');
        if (!stateStr) return false;
        
        try {
            const state = JSON.parse(stateStr);
            
            // Restore input values
            if (searchInput && state.searchQuery) searchInput.value = state.searchQuery;
            if (minYear && state.minYear) minYear.value = state.minYear;
            if (maxYear && state.maxYear) maxYear.value = state.maxYear;
            if (minPrice && state.minPrice) minPrice.value = state.minPrice;
            if (maxPrice && state.maxPrice) maxPrice.value = state.maxPrice;
            if (minMileage && state.minMileage) minMileage.value = state.minMileage;
            if (maxMileage && state.maxMileage) maxMileage.value = state.maxMileage;
            
            // Restore mileage unit
            if (state.mileageUnit) {
                const unitBtn = document.querySelector(`.mileage-unit-btn[data-unit="${state.mileageUnit}"]`);
                if (unitBtn) {
                    document.querySelectorAll('.mileage-unit-btn').forEach(btn => btn.classList.remove('active'));
                    unitBtn.classList.add('active');
                }
            }
            
            // Update range selector trigger texts
            const yearRangeTrigger = document.getElementById('yearRangeTrigger');
            if (yearRangeTrigger && (state.minYear || state.maxYear)) {
                const textEl = yearRangeTrigger.querySelector('.multi-select-text');
                if (textEl) {
                    textEl.textContent = (state.minYear || 'Any') + ' - ' + (state.maxYear || 'Any');
                }
            }
            
            const priceRangeTrigger = document.getElementById('priceRangeTrigger');
            if (priceRangeTrigger && (state.minPrice || state.maxPrice)) {
                const textEl = priceRangeTrigger.querySelector('.multi-select-text');
                if (textEl) {
                    textEl.textContent = '$' + (state.minPrice || 'Any') + ' - $' + (state.maxPrice || 'Any');
                }
            }
            
            const mileageRangeTrigger = document.getElementById('mileageRangeTrigger');
            if (mileageRangeTrigger && (state.minMileage || state.maxMileage)) {
                const textEl = mileageRangeTrigger.querySelector('.multi-select-text');
                if (textEl) {
                    const unitLabel = state.mileageUnit === 'km' ? 'km' : 'miles';
                    textEl.textContent = (state.minMileage || 'Any') + ' - ' + (state.maxMileage || 'Any') + ' ' + unitLabel;
                }
            }
            
            // Restore vehicle types
            if (state.vehicleTypes && state.vehicleTypes.length > 0) {
                const vehicleTypeAll = document.getElementById('vehicleTypeAll');
                if (vehicleTypeAll) vehicleTypeAll.checked = false;
                state.vehicleTypes.forEach(type => {
                    const checkbox = document.getElementById(`vehicleType_${type}`);
                    if (checkbox) checkbox.checked = true;
                });
                // Manually update trigger text
                const vehicleTypeTrigger = document.getElementById('vehicleTypeTrigger');
                if (vehicleTypeTrigger) {
                    const textEl = vehicleTypeTrigger.querySelector('.multi-select-text');
                    if (textEl) {
                        if (state.vehicleTypes.length === 1) {
                            textEl.textContent = state.vehicleTypes[0] === 'motorcycle' ? 'Motorcycle' : state.vehicleTypes[0].charAt(0).toUpperCase() + state.vehicleTypes[0].slice(1);
                        } else {
                            textEl.textContent = `${state.vehicleTypes.length} selected`;
                        }
                        vehicleTypeTrigger.style.color = 'var(--text-primary)';
                    }
                }
            }
            
            // Restore makes
            if (state.makes && state.makes.length > 0) {
                const makeAll = document.getElementById('makeAll');
                if (makeAll) makeAll.checked = false;
                state.makes.forEach(make => {
                    const checkbox = document.getElementById(`make_${make}`);
                    if (checkbox) checkbox.checked = true;
                });
                // Manually update trigger text
                const makeTrigger = document.getElementById('makeTrigger');
                if (makeTrigger) {
                    const textEl = makeTrigger.querySelector('.multi-select-text');
                    if (textEl) {
                        if (state.makes.length === 1) {
                            textEl.textContent = state.makes[0];
                        } else {
                            textEl.textContent = `${state.makes.length} selected`;
                        }
                        makeTrigger.style.color = 'var(--text-primary)';
                    }
                }
            }
            
            // Restore transmission
            if (state.transmission) {
                const radio = document.querySelector(`input[name="transmission"][value="${state.transmission}"]`);
                if (radio) {
                    radio.checked = true;
                    const transmissionTrigger = document.getElementById('transmissionTrigger');
                    if (transmissionTrigger) {
                        const textEl = transmissionTrigger.querySelector('.multi-select-text');
                        if (textEl) {
                            textEl.textContent = state.transmission;
                            transmissionTrigger.style.color = 'var(--text-primary)';
                        }
                    }
                }
            }
            
            // Restore fuel type
            if (state.fuelType) {
                const radio = document.querySelector(`input[name="fuelType"][value="${state.fuelType}"]`);
                if (radio) {
                    radio.checked = true;
                    const fuelTypeTrigger = document.getElementById('fuelTypeTrigger');
                    if (fuelTypeTrigger) {
                        const textEl = fuelTypeTrigger.querySelector('.multi-select-text');
                        if (textEl) {
                            textEl.textContent = state.fuelType;
                            fuelTypeTrigger.style.color = 'var(--text-primary)';
                        }
                    }
                }
            }
            
            // Restore color
            if (state.color) {
                const radio = document.querySelector(`input[name="color"][value="${state.color}"]`);
                if (radio) {
                    radio.checked = true;
                    const colorTrigger = document.getElementById('colorTrigger');
                    if (colorTrigger) {
                        const textEl = colorTrigger.querySelector('.multi-select-text');
                        if (textEl) {
                            textEl.textContent = state.color;
                            colorTrigger.style.color = 'var(--text-primary)';
                        }
                    }
                }
            }
            
            // Restore scroll position after a short delay
            if (state.scrollPosition) {
                setTimeout(() => {
                    window.scrollTo(0, state.scrollPosition);
                }, 500);
            }
            
            // Clear the stored state after restoring
            sessionStorage.removeItem('searchPageState');
            
            return true;
        } catch (error) {
            sessionStorage.removeItem('searchPageState');
            return false;
        }
    }

    // Make performSearch globally available
    window.performSearch = performSearch;
    
    // Ensure multiselect filters are initialized (backup in case main.js hasn't run yet)
    if (typeof initMultiSelect === 'function') {
        initMultiSelect();
    } else {
        setTimeout(() => {
            if (typeof initMultiSelect === 'function') {
                initMultiSelect();
            }
        }, 200);
    }
    
    // Restore search state if returning from detail page
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('from') === 'detail') {
        setTimeout(() => {
            if (restoreSearchState()) {
                performSearch();
            } else {
                performSearch();
            }
        }, 300);
    } else {
        // Perform initial search to show all cars
        setTimeout(() => {
            performSearch();
        }, 100);
    }
    
    // Close all dropdowns on scroll (additional handler for search page)
    // The global handler in multiselect.js will also work, but this ensures it works here too
    let searchPageScrollTimeout = null;
    function handleSearchPageScroll() {
        if (searchPageScrollTimeout) {
            clearTimeout(searchPageScrollTimeout);
        }
        searchPageScrollTimeout = setTimeout(function() {
            if (typeof closeAllDropdowns === 'function') {
                closeAllDropdowns();
            }
        }, 50);
    }
    
    window.addEventListener('scroll', handleSearchPageScroll, { passive: true });
    window.addEventListener('touchmove', handleSearchPageScroll, { passive: true });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchPage);
} else {
    initSearchPage();
}


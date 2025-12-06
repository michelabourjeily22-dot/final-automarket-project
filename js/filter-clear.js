/**
 * Filter Clear Button Functionality
 * Adds clear (X) buttons to all filters on search page
 */

/**
 * Create clear button HTML element
 */
function createClearButton() {
    const btn = document.createElement('button');
    btn.className = 'clear-filter-btn';
    btn.type = 'button';
    btn.setAttribute('aria-label', 'Clear filter');
    btn.innerHTML = '×';
    return btn;
}

/**
 * Wrap filter wrapper (multi-select-wrapper or range-selector-wrapper) in filter-wrapper container
 */
function wrapFilterWrapper(filterWrapper) {
    // Check if already wrapped
    if (filterWrapper.parentElement && filterWrapper.parentElement.classList.contains('filter-wrapper')) {
        return filterWrapper.parentElement;
    }
    
    // Create wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'filter-wrapper';
    wrapper.style.position = 'relative';
    wrapper.style.display = 'inline-block';
    wrapper.style.width = '100%';
    
    // Wrap the filter wrapper
    filterWrapper.parentNode.insertBefore(wrapper, filterWrapper);
    wrapper.appendChild(filterWrapper);
    
    return wrapper;
}

/**
 * Clear multi-select filter
 */
function clearMultiSelectFilter(triggerId, dropdownId, defaultText, allCheckboxId) {
    const trigger = document.getElementById(triggerId);
    const dropdown = document.getElementById(dropdownId);
    
    if (!trigger || !dropdown) return;
    
    // Uncheck all checkboxes
    const checkboxes = dropdown.querySelectorAll('.multi-select-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    
    // Uncheck "Select All" checkbox if exists
    const allCheckbox = document.getElementById(allCheckboxId);
    if (allCheckbox) {
        allCheckbox.checked = false;
        allCheckbox.indeterminate = false;
    }
    
    // Reset trigger text
    const textElement = trigger.querySelector('.multi-select-text');
    if (textElement) {
        textElement.textContent = defaultText;
    }
    
    // Reset trigger color
    trigger.style.color = 'var(--text-secondary)';
    
    // Close dropdown
    dropdown.classList.remove('active');
    trigger.classList.remove('active');
    
    // Update wrapper has-value state
    const wrapper = trigger.closest('.filter-wrapper');
    if (wrapper) {
        wrapper.classList.remove('has-value');
    }
    
    // Trigger search update
    if (typeof performSearch === 'function') {
        performSearch();
    } else if (typeof window.performSearch === 'function') {
        window.performSearch();
    }
}

/**
 * Clear range filter
 */
function clearRangeFilter(minInputId, maxInputId, triggerId, defaultText) {
    const minInput = document.getElementById(minInputId);
    const maxInput = document.getElementById(maxInputId);
    const trigger = document.getElementById(triggerId);
    
    if (minInput) {
        minInput.value = '';
        minInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (maxInput) {
        maxInput.value = '';
        maxInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
    
    if (trigger) {
        const textElement = trigger.querySelector('.multi-select-text');
        if (textElement) {
            textElement.textContent = defaultText;
        }
        trigger.style.color = 'var(--text-secondary)';
        
        // Close dropdown if open
        const wrapper = trigger.closest('.filter-wrapper');
        if (!wrapper) {
            const rangeWrapper = trigger.closest('.range-selector-wrapper, .multi-select-wrapper');
            if (rangeWrapper) {
                const parentWrapper = rangeWrapper.closest('.filter-wrapper');
                if (parentWrapper) {
                    parentWrapper.classList.remove('has-value');
                }
            }
        } else {
            wrapper.classList.remove('has-value');
        }
        
        const dropdown = trigger.closest('.range-selector-wrapper, .multi-select-wrapper')?.querySelector('.range-selector-dropdown, .multi-select-dropdown');
        if (dropdown) {
            dropdown.classList.remove('active');
        }
        trigger.classList.remove('active');
    }
    
    // Trigger search update
    if (typeof performSearch === 'function') {
        performSearch();
    } else if (typeof window.performSearch === 'function') {
        window.performSearch();
    }
}

/**
 * Clear search input
 */
function clearSearchInput() {
    const searchInput = document.getElementById('searchInput');
    const wrapper = document.querySelector('.search-input-wrapper');
    
    if (searchInput) {
        searchInput.value = '';
        if (wrapper) {
            wrapper.classList.remove('has-value');
        }
    }
    
    // Trigger search update
    if (typeof performSearch === 'function') {
        performSearch();
    } else if (typeof window.performSearch === 'function') {
        window.performSearch();
    }
}

/**
 * Add clear button to a filter wrapper
 */
function addClearButtonToFilter(triggerId, clearFunction, hasValueCheckFunction) {
    const trigger = document.getElementById(triggerId);
    if (!trigger) return;
    
    // Find the multi-select-wrapper or range-selector-wrapper that contains the trigger
    const filterWrapper = trigger.closest('.multi-select-wrapper, .range-selector-wrapper');
    if (!filterWrapper) return;
    
    // Wrap the filter wrapper if not already wrapped
    const wrapper = wrapFilterWrapper(filterWrapper);
    
    // Check if clear button already exists
    if (wrapper.querySelector('.clear-filter-btn')) return;
    
    // Create and add clear button
    const clearBtn = createClearButton();
    wrapper.appendChild(clearBtn);
    
    // Add click handler
    clearBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();
        clearFunction();
    }, true);
    
    // Update visibility based on current state
    if (hasValueCheckFunction) {
        updateClearButtonVisibility(wrapper, hasValueCheckFunction);
    }
}

/**
 * Update clear button visibility
 */
function updateClearButtonVisibility(wrapper, checkFunction) {
    if (!wrapper) return;
    const hasValue = checkFunction();
    if (hasValue) {
        wrapper.classList.add('has-value');
    } else {
        wrapper.classList.remove('has-value');
    }
}

/**
 * Initialize all clear buttons on search page
 */
function initFilterClearButtons() {
    // Only run on search page
    if (!document.getElementById('searchInput')) return;
    
    // Helper functions to check if filter has values
    function hasVehicleTypes() {
        const checkboxes = document.querySelectorAll('#vehicleTypeDropdown .multi-select-checkbox:not(#vehicleTypeAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasMakes() {
        const checkboxes = document.querySelectorAll('#makeDropdown .multi-select-checkbox:not(#makeAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasModels() {
        const checkboxes = document.querySelectorAll('#modelDropdown .multi-select-checkbox:not(#modelAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasTransmissions() {
        const checkboxes = document.querySelectorAll('#transmissionDropdown .multi-select-checkbox:not(#transmissionAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasFuelTypes() {
        const checkboxes = document.querySelectorAll('#fuelTypeDropdown .multi-select-checkbox:not(#fuelTypeAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasColors() {
        const checkboxes = document.querySelectorAll('#colorDropdown .multi-select-checkbox:not(#colorAll)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }
    
    function hasYearRange() {
        const minYear = document.getElementById('minYear');
        const maxYear = document.getElementById('maxYear');
        return (minYear && minYear.value.trim()) || (maxYear && maxYear.value.trim());
    }
    
    function hasPriceRange() {
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        return (minPrice && minPrice.value.trim()) || (maxPrice && maxPrice.value.trim());
    }
    
    function hasMileageRange() {
        const minMileage = document.getElementById('minMileage');
        const maxMileage = document.getElementById('maxMileage');
        return (minMileage && minMileage.value.trim()) || (maxMileage && maxMileage.value.trim());
    }
    
    // Add clear buttons to multi-select filters
    addClearButtonToFilter('vehicleTypeTrigger', function() {
        clearMultiSelectFilter('vehicleTypeTrigger', 'vehicleTypeDropdown', 'All Types', 'vehicleTypeAll');
    }, hasVehicleTypes);
    
    addClearButtonToFilter('makeTrigger', function() {
        clearMultiSelectFilter('makeTrigger', 'makeDropdown', 'All Makes', 'makeAll');
    }, hasMakes);
    
    addClearButtonToFilter('modelTrigger', function() {
        clearMultiSelectFilter('modelTrigger', 'modelDropdown', 'Model', 'modelAll');
    }, hasModels);
    
    addClearButtonToFilter('transmissionTrigger', function() {
        clearMultiSelectFilter('transmissionTrigger', 'transmissionDropdown', 'All Transmissions', 'transmissionAll');
    }, hasTransmissions);
    
    addClearButtonToFilter('fuelTypeTrigger', function() {
        clearMultiSelectFilter('fuelTypeTrigger', 'fuelTypeDropdown', 'All Fuel Types', 'fuelTypeAll');
    }, hasFuelTypes);
    
    addClearButtonToFilter('colorTrigger', function() {
        clearMultiSelectFilter('colorTrigger', 'colorDropdown', 'All Colors', 'colorAll');
    }, hasColors);
    
    // Add clear buttons to range filters
    addClearButtonToFilter('yearRangeTrigger', function() {
        clearRangeFilter('minYear', 'maxYear', 'yearRangeTrigger', 'Year Range');
    }, hasYearRange);
    
    addClearButtonToFilter('priceRangeTrigger', function() {
        clearRangeFilter('minPrice', 'maxPrice', 'priceRangeTrigger', 'Price Range');
    }, hasPriceRange);
    
    addClearButtonToFilter('mileageRangeTrigger', function() {
        clearRangeFilter('minMileage', 'maxMileage', 'mileageRangeTrigger', 'Mileage Range');
    }, hasMileageRange);
    
    // Add clear button to search input
    const searchInput = document.getElementById('searchInput');
    const searchWrapper = document.querySelector('.search-input-wrapper');
    if (searchInput && searchWrapper) {
        if (!searchWrapper.querySelector('.clear-filter-btn')) {
            const clearBtn = createClearButton();
            clearBtn.style.position = 'absolute';
            clearBtn.style.top = '50%';
            clearBtn.style.right = '12px';
            clearBtn.style.transform = 'translateY(-50%)';
            searchWrapper.appendChild(clearBtn);
            
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                clearSearchInput();
            }, true);
            
            searchInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    searchWrapper.classList.add('has-value');
                } else {
                    searchWrapper.classList.remove('has-value');
                }
            });
        }
    }
    
    // Setup delegated event listeners to update clear button visibility when filters change
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('multi-select-checkbox')) {
            const dropdown = e.target.closest('.multi-select-dropdown');
            if (dropdown) {
                const triggerIdMap = {
                    'vehicleTypeDropdown': { trigger: 'vehicleTypeTrigger', check: hasVehicleTypes },
                    'makeDropdown': { trigger: 'makeTrigger', check: hasMakes },
                    'modelDropdown': { trigger: 'modelTrigger', check: hasModels },
                    'transmissionDropdown': { trigger: 'transmissionTrigger', check: hasTransmissions },
                    'fuelTypeDropdown': { trigger: 'fuelTypeTrigger', check: hasFuelTypes },
                    'colorDropdown': { trigger: 'colorTrigger', check: hasColors }
                };
                
                const config = triggerIdMap[dropdown.id];
                if (config) {
                    setTimeout(function() {
                        const trigger = document.getElementById(config.trigger);
                        if (trigger) {
                            const wrapper = trigger.closest('.filter-wrapper');
                            if (wrapper) {
                                updateClearButtonVisibility(wrapper, config.check);
                            }
                        }
                    }, 10);
                }
            }
        }
    });
    
    // Also listen to range input changes
    ['minYear', 'maxYear', 'minPrice', 'maxPrice', 'minMileage', 'maxMileage'].forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                let triggerId, checkFunction;
                if (inputId.includes('Year')) {
                    triggerId = 'yearRangeTrigger';
                    checkFunction = hasYearRange;
                } else if (inputId.includes('Price')) {
                    triggerId = 'priceRangeTrigger';
                    checkFunction = hasPriceRange;
                } else if (inputId.includes('Mileage')) {
                    triggerId = 'mileageRangeTrigger';
                    checkFunction = hasMileageRange;
                }
                
                if (triggerId && checkFunction) {
                    const trigger = document.getElementById(triggerId);
                    if (trigger) {
                        const wrapper = trigger.closest('.filter-wrapper');
                        if (wrapper) {
                            updateClearButtonVisibility(wrapper, checkFunction);
                        }
                    }
                }
            });
        }
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initFilterClearButtons, 100);
    });
} else {
    setTimeout(initFilterClearButtons, 100);
}

// Re-attach clear buttons after dynamic dropdown updates
const originalPopulateMakeDropdown = window.populateMakeDropdown;
const originalUpdateSearchModelDropdown = window.updateSearchModelDropdown;

if (originalPopulateMakeDropdown) {
    window.populateMakeDropdown = function() {
        originalPopulateMakeDropdown();
        setTimeout(function() {
            const trigger = document.getElementById('makeTrigger');
            if (trigger) {
                addClearButtonToFilter('makeTrigger', function() {
                    clearMultiSelectFilter('makeTrigger', 'makeDropdown', 'All Makes', 'makeAll');
                }, function() {
                    const checkboxes = document.querySelectorAll('#makeDropdown .multi-select-checkbox:not(#makeAll)');
                    return Array.from(checkboxes).some(cb => cb.checked);
                });
            }
        }, 50);
    };
}

if (originalUpdateSearchModelDropdown) {
    window.updateSearchModelDropdown = function(selectedMakes) {
        originalUpdateSearchModelDropdown(selectedMakes);
        setTimeout(function() {
            const trigger = document.getElementById('modelTrigger');
            if (trigger) {
                addClearButtonToFilter('modelTrigger', function() {
                    clearMultiSelectFilter('modelTrigger', 'modelDropdown', 'Model', 'modelAll');
                }, function() {
                    const checkboxes = document.querySelectorAll('#modelDropdown .multi-select-checkbox:not(#modelAll)');
                    return Array.from(checkboxes).some(cb => cb.checked);
                });
            }
        }, 50);
    };
}

// Make functions globally available
window.clearMultiSelectFilter = clearMultiSelectFilter;
window.clearRangeFilter = clearRangeFilter;
window.clearSearchInput = clearSearchInput;
window.initFilterClearButtons = initFilterClearButtons;


/**
 * Post Car Page Input Enhancements
 * Year, Mileage, and Price input customizations
 */

/**
 * Initialize Post Car page input enhancements
 */
function initPostInputs() {
    // Only run on Post Car page
    if (!document.getElementById('year') || !document.getElementById('mileage') || !document.getElementById('price')) {
        return;
    }
    
    initYearInput();
    initMileageInput();
    initPriceInput();
}

/**
 * Initialize Year Input
 * - Default min: 2000, max: 2026
 * - Step: 1 year per click
 * - Custom spinner behavior
 */
function initYearInput() {
    const yearInput = document.getElementById('year');
    if (!yearInput) return;
    
    // Set default attributes (no min to allow any year)
    yearInput.max = '2026';
    yearInput.step = '1';
    
    // Hide native spinner
    yearInput.style.webkitAppearance = 'none';
    yearInput.style.mozAppearance = 'textfield';
    
    // Handle wheel events for increment/decrement
    yearInput.addEventListener('wheel', function(e) {
        e.preventDefault();
        const currentValue = parseInt(this.value) || 0;
        const delta = e.deltaY < 0 ? 1 : -1;
        let newValue = currentValue + delta;
        
        if (newValue > 2026) newValue = 2026;
        
        this.value = newValue;
        this.dispatchEvent(new Event('input', { bubbles: true }));
    });
}

/**
 * Initialize Mileage Input
 * - Step: 1 (allows any number)
 * - Miles/KM toggle
 * - Default: Miles, convert on toggle
 */
function initMileageInput() {
    const mileageInput = document.getElementById('mileage');
    if (!mileageInput) return;
    
    // Set step to 1 to allow any number
    mileageInput.step = '1';
    mileageInput.min = '0';
    
    // Hide native spinner
    mileageInput.style.webkitAppearance = 'none';
    mileageInput.style.mozAppearance = 'textfield';
    
    // Get form group wrapper
    const formGroup = mileageInput.closest('.form-group');
    if (!formGroup) return;
    
    // Create Miles/KM toggle
    let isMiles = true; // Default to miles
    
    // Check if toggle already exists
    if (formGroup.querySelector('.mileage-unit-toggle')) {
        return; // Already initialized
    }
    
    const toggleContainer = document.createElement('div');
    toggleContainer.className = 'mileage-unit-toggle';
    toggleContainer.style.cssText = 'display: flex; gap: 8px; margin-top: 8px; align-items: center;';
    
    const milesBtn = document.createElement('button');
    milesBtn.type = 'button';
    milesBtn.className = 'unit-btn active';
    milesBtn.textContent = 'Miles';
    milesBtn.style.cssText = 'padding: 6px 12px; border: 1px solid var(--border-color); background: var(--primary-color); color: white; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.15s;';
    
    const kmBtn = document.createElement('button');
    kmBtn.type = 'button';
    kmBtn.className = 'unit-btn';
    kmBtn.textContent = 'KM';
    kmBtn.style.cssText = 'padding: 6px 12px; border: 1px solid var(--border-color); background: rgba(26, 26, 26, 0.6); color: var(--primary-color); border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.15s;';
    
    // Toggle function
    function switchToMiles() {
        isMiles = true;
        milesBtn.classList.add('active');
        kmBtn.classList.remove('active');
        milesBtn.style.background = 'var(--primary-color)';
        milesBtn.style.color = 'white';
        kmBtn.style.background = 'rgba(26, 26, 26, 0.6)';
        kmBtn.style.color = 'var(--text-secondary)';
        
        // Convert KM to Miles if there's a value
        if (mileageInput.value && mileageInput.value !== '') {
            const kmValue = parseFloat(mileageInput.value);
            if (!isNaN(kmValue)) {
                const milesValue = Math.round(kmValue * 0.621371);
                mileageInput.value = milesValue;
            }
        }
        
        // Update placeholder
        mileageInput.placeholder = 'e.g., 25000';
    }
    
    function switchToKM() {
        isMiles = false;
        kmBtn.classList.add('active');
        milesBtn.classList.remove('active');
        kmBtn.style.background = 'var(--primary-color)';
        kmBtn.style.color = 'white';
        milesBtn.style.background = 'rgba(26, 26, 26, 0.6)';
        milesBtn.style.color = 'var(--text-secondary)';
        
        // Convert Miles to KM if there's a value
        if (mileageInput.value && mileageInput.value !== '') {
            const milesValue = parseFloat(mileageInput.value);
            if (!isNaN(milesValue)) {
                const kmValue = Math.round(milesValue * 1.60934);
                mileageInput.value = kmValue;
            }
        }
        
        // Update placeholder
        mileageInput.placeholder = 'e.g., 40000';
    }
    
    milesBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (!isMiles) {
            switchToMiles();
        }
    });
    
    kmBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (isMiles) {
            switchToKM();
        }
    });
    
    toggleContainer.appendChild(milesBtn);
    toggleContainer.appendChild(kmBtn);
    formGroup.appendChild(toggleContainer);
    
    // Ensure value is always saved in miles
    mileageInput.addEventListener('change', function() {
        if (!isMiles && this.value && this.value !== '') {
            // Convert KM to miles for submission (but keep KM display)
            const kmValue = parseFloat(this.value);
            if (!isNaN(kmValue)) {
                const milesValue = Math.round(kmValue * 0.621371);
                // Store in hidden field or data attribute
                this.setAttribute('data-miles-value', milesValue);
            }
        }
    });
    
    // On form submit, ensure value is in miles
    const form = mileageInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!isMiles && mileageInput.value && mileageInput.value !== '') {
                const kmValue = parseFloat(mileageInput.value);
                if (!isNaN(kmValue)) {
                    const milesValue = Math.round(kmValue * 0.621371);
                    mileageInput.value = milesValue; // Submit in miles
                }
            }
        });
    }
}

/**
 * Initialize Price Input
 * - Step: any (allows any number including decimals)
 * - Default behavior for empty state
 */
function initPriceInput() {
    const priceInput = document.getElementById('price');
    if (!priceInput) return;
    
    // Set step to 'any' to allow any number
    priceInput.step = 'any';
    priceInput.min = '0';
    
    // Hide native spinner
    priceInput.style.webkitAppearance = 'none';
    priceInput.style.mozAppearance = 'textfield';
}

// Initialize when DOM is ready
function initPostInputsWhenReady() {
    // Wait a bit for any dynamic content to load
    setTimeout(function() {
        initPostInputs();
    }, 100);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPostInputsWhenReady);
} else {
    initPostInputsWhenReady();
}

// Re-initialize if form is dynamically added or reset
document.addEventListener('DOMContentLoaded', function() {
    // Re-initialize after a short delay to catch any late-loading content
    setTimeout(function() {
        if (document.getElementById('year') && document.getElementById('mileage')) {
            initPostInputs();
        }
    }, 300);
});

// Make globally available
window.initPostInputs = initPostInputs;


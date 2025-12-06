// Main JavaScript file - Initializes all modules

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the add post form page (add_post.php)
    const isAddListingPage = document.getElementById('listingForm') && 
                             document.getElementById('vehicleTypeTrigger');
    
    // Initialize form dropdowns FIRST if on add listing page
    if (isAddListingPage && typeof initFormDropdowns === 'function') {
        initFormDropdowns();
    }
    
    // Initialize all modules
    if (typeof initMultiSelect === 'function') {
        initMultiSelect();
    }
    
    if (typeof initImagePreview === 'function') {
        initImagePreview();
    }
    
    if (typeof initSearchFunctionality === 'function') {
        initSearchFunctionality();
    }
    
    if (typeof initFormValidation === 'function') {
        initFormValidation();
    }
    
    if (typeof initSuccessToast === 'function') {
        initSuccessToast();
    }
    
    if (typeof initModalCloseOnClickOutside === 'function') {
        initModalCloseOnClickOutside();
    }
});
// Toast Notification Management

/**
 * Initialize success toast auto-hide
 */
function initSuccessToast() {
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s';
            setTimeout(function() {
                toast.style.display = 'none';
            }, 500);
        }, 3000);
    }
}

/**
 * Show toast message
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, info)
 */
function showToast(message, type = 'success') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 3000);
}


// Modal Management

function openModalById(id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Focus search input when opening search modal
    if (id === 'searchModal') {
        setTimeout(() => {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.focus();
        }, 100);
    }
}

/**
 * Open search modal
 */
function openSearchModal() {
    openModalById('searchModal');
}

/**
 * Close search modal
 */
function closeSearchModal() {
    const modal = document.getElementById('searchModal');
    if (modal) {
        modal.classList.remove('active');

        // Restore scroll only if no other modal is open
        const anyOpen = document.querySelector('.modal.active');
        if (!anyOpen) {
            document.body.style.overflow = '';
        }
        // Clear search and all filters
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        
        // Clear multi-select checkboxes
        const makeCheckboxes = document.querySelectorAll('#makeDropdown .multi-select-checkbox');
        makeCheckboxes.forEach(cb => cb.checked = false);
        
        const yearCheckboxes = document.querySelectorAll('#yearDropdown .multi-select-checkbox');
        yearCheckboxes.forEach(cb => cb.checked = false);
        
        // Close dropdowns
        document.querySelectorAll('.multi-select-dropdown').forEach(dd => {
            dd.classList.remove('active');
            dd.previousElementSibling?.classList.remove('active');
        });
        
        // Update trigger texts
        const makeTrigger = document.getElementById('makeTrigger');
        if (makeTrigger) {
            makeTrigger.querySelector('.multi-select-text').textContent = 'All Makes';
            makeTrigger.style.color = 'var(--text-secondary)';
        }
        
        const yearTrigger = document.getElementById('yearTrigger');
        if (yearTrigger) {
            yearTrigger.querySelector('.multi-select-text').textContent = 'All Years';
            yearTrigger.style.color = 'var(--text-secondary)';
        }
        
        const maxPrice = document.getElementById('maxPrice');
        if (maxPrice) maxPrice.value = '';
        const results = document.getElementById('searchResults');
        if (results) results.innerHTML = '';
    }
}


/**
 * Initialize modal close on outside click and Escape key
 */
function initModalCloseOnClickOutside() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                if (modal.id === 'searchModal') {
                    closeSearchModal();
                }
                if (modal.id === 'authModal') {
                    closeAuthModal();
                }
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const searchModal = document.getElementById('searchModal');
            if (searchModal && searchModal.classList.contains('active')) {
                closeSearchModal();
            }

            const authModal = document.getElementById('authModal');
            if (authModal && authModal.classList.contains('active')) {
                closeAuthModal();
            }
        }
    });
}

function openAuthModal() {
    openModalById('authModal');
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (!modal) return;

    modal.classList.remove('active');

    const anyOpen = document.querySelector('.modal.active');
    if (!anyOpen) {
        document.body.style.overflow = '';
    }
}

// Make functions globally available
window.openSearchModal = openSearchModal;
window.closeSearchModal = closeSearchModal;
window.openAuthModal = openAuthModal;
window.closeAuthModal = closeAuthModal;

document.addEventListener('DOMContentLoaded', initModalCloseOnClickOutside);

function openSignupFromLogin() {
    const loginSection = document.getElementById('authLogin');
    const signupSection = document.getElementById('authSignup');
    const title = document.getElementById('authTitle');

    if (!loginSection || !signupSection || !title) return;

    // Try to carry over email from login identifier
    const identifierInput = document.getElementById('loginIdentifier');
    const signupEmail = document.getElementById('signupEmail');
    if (identifierInput && signupEmail) {
        const value = identifierInput.value.trim();
        if (value.includes('@') && !signupEmail.value) {
            signupEmail.value = value;
        }
    }

    loginSection.classList.remove('active');
    signupSection.classList.add('active');
    title.textContent = 'Sign up';
}

function openLoginFromSignup() {
    const loginSection = document.getElementById('authLogin');
    const signupSection = document.getElementById('authSignup');
    const title = document.getElementById('authTitle');

    if (!loginSection || !signupSection || !title) return;

    signupSection.classList.remove('active');
    loginSection.classList.add('active');
    title.textContent = 'Log in';
}

window.openSignupFromLogin = openSignupFromLogin;
window.openLoginFromSignup = openLoginFromSignup;


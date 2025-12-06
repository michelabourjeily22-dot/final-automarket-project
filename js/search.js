// Search Functionality

/**
 * Initialize search functionality
 */
function initSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    const maxPrice = document.getElementById('maxPrice');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput || !searchResults) return;

    const allCars = Array.from(document.querySelectorAll('.feed-post')).map(post => {
        const titleEl = post.querySelector('.post-title strong');
        const priceEl = post.querySelector('.post-price');
        const descEl = post.querySelector('.post-description');
        const carId = post.getAttribute('data-car-id') || '';
        const carImage = post.getAttribute('data-car-image') || '';
        
        // Extract year from description (format: "2020 • 25000 miles • Silver")
        const description = descEl ? descEl.textContent : '';
        const yearMatch = description.match(/(\d{4})/);
        const year = yearMatch ? yearMatch[1] : '';
        
        return {
            element: post,
            id: carId,
            image: carImage,
            title: titleEl ? titleEl.textContent : '',
            price: priceEl ? priceEl.textContent.replace('$', '').replace(/,/g, '') : '',
            description: description,
            make: titleEl ? titleEl.textContent.split(' ')[0] : '',
            model: titleEl ? titleEl.textContent.split(' ').slice(1).join(' ') : '',
            year: year
        };
    });

    /**
     * Perform search and filter
     */
    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        
        // Get selected makes and years from multi-select
        const selectedMakes = typeof getSelectedMakes === 'function' ? getSelectedMakes() : [];
        const selectedYears = typeof getSelectedYears === 'function' ? getSelectedYears() : [];
        
        const priceFilter = maxPrice && maxPrice.value ? parseFloat(maxPrice.value) : null;

        const filteredCars = allCars.filter(car => {
            let matches = true;

            // Text search
            if (query) {
                const searchText = (car.title + ' ' + car.description).toLowerCase();
                if (!searchText.includes(query)) {
                    matches = false;
                }
            }

            // Make filter - check if car make is in selected makes array
            if (selectedMakes.length > 0) {
                if (!selectedMakes.includes(car.make)) {
                    matches = false;
                }
            }

            // Year filter - check if car year is in selected years array
            if (selectedYears.length > 0) {
                if (!car.year || !selectedYears.includes(car.year)) {
                    matches = false;
                }
            }

            // Price filter
            if (priceFilter !== null && car.price) {
                const carPrice = parseFloat(car.price);
                if (isNaN(carPrice) || carPrice > priceFilter) {
                    matches = false;
                }
            }

            return matches;
        });

        displaySearchResults(filteredCars);
    }
    
    // Make performSearch globally available
    window.performSearch = performSearch;

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
                    // Close modal and navigate to detail page
                    if (typeof closeSearchModal === 'function') {
                        closeSearchModal();
                    }
                    // Navigate to detail page
                    window.location.href = `car_detail.php?id=${encodeURIComponent(car.id)}`;
                }
            });
            
            searchResults.appendChild(resultItem);
        });
    }

    // Event listeners - only search when user interacts
    searchInput.addEventListener('input', performSearch);
    if (maxPrice) maxPrice.addEventListener('input', performSearch);

    // Don't perform initial search - wait for user input
    // Results will only show when user types or selects filters
}


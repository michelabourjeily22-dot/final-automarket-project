// Utility Functions

/**
 * Format number with commas
 * @param {number|string} num - Number to format
 * @returns {string} Formatted number string
 */
function formatNumber(num) {
    if (!num) return '0';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Debounce function to limit function calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Parse XML response to JavaScript object/array
 * @param {string} xmlString - XML string to parse
 * @returns {Object|Array|null} Parsed data structure
 */
function parseXMLResponse(xmlString) {
    try {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlString, 'text/xml');
        
        // Check for parsing errors
        const parserError = xmlDoc.querySelector('parsererror');
        if (parserError) {
            console.error('XML parsing error:', parserError.textContent);
            return null;
        }
        
        // Check if this is an error response
        const errorNode = xmlDoc.querySelector('error');
        if (errorNode) {
            return {
                success: false,
                error: errorNode.textContent
            };
        }
        
        // Check for success response with cars (handle both <response><cars> and direct <cars>)
        const responseNode = xmlDoc.querySelector('response');
        const carsNode = responseNode ? responseNode.querySelector('cars') : xmlDoc.querySelector('cars');
        if (carsNode) {
            const cars = Array.from(carsNode.querySelectorAll('car')).map(carNode => {
                return {
                    id: getXMLText(carNode, 'id'),
                    make: getXMLText(carNode, 'make'),
                    model: getXMLText(carNode, 'model'),
                    year: parseInt(getXMLText(carNode, 'year')) || 0,
                    price: parseFloat(getXMLText(carNode, 'price')) || 0,
                    mileage: parseInt(getXMLText(carNode, 'mileage')) || 0,
                    color: getXMLText(carNode, 'color'),
                    fuel: getXMLText(carNode, 'fuel'),
                    transmission: getXMLText(carNode, 'transmission'),
                    vehicle_type: getXMLText(carNode, 'vehicle_type') || 'car',
                    created_at: getXMLText(carNode, 'created_at'),
                    images: Array.from(carNode.querySelectorAll('image')).map(img => img.textContent).filter(img => img)
                };
            });
            
            const rootNode = responseNode || xmlDoc;
            const countNode = rootNode.querySelector('count');
            const successNode = rootNode.querySelector('success');
            
            return {
                success: successNode ? successNode.textContent === 'true' : true,
                results: cars,
                cars: cars, // Alias for compatibility
                count: countNode ? parseInt(countNode.textContent) : cars.length
            };
        }
        
        // Check for single car response (handle both <response><car> and direct <car>)
        const responseNodeForCar = xmlDoc.querySelector('response');
        const carNode = responseNodeForCar ? responseNodeForCar.querySelector('car') : xmlDoc.querySelector('car');
        if (carNode && (!responseNodeForCar || responseNodeForCar.querySelector('cars') === null)) {
            const car = {
                id: getXMLText(carNode, 'id'),
                make: getXMLText(carNode, 'make'),
                model: getXMLText(carNode, 'model'),
                year: parseInt(getXMLText(carNode, 'year')) || 0,
                price: parseFloat(getXMLText(carNode, 'price')) || 0,
                mileage: parseInt(getXMLText(carNode, 'mileage')) || 0,
                color: getXMLText(carNode, 'color'),
                fuel: getXMLText(carNode, 'fuel'),
                transmission: getXMLText(carNode, 'transmission'),
                vehicle_type: getXMLText(carNode, 'vehicle_type') || 'car',
                created_at: getXMLText(carNode, 'created_at'),
                images: Array.from(carNode.querySelectorAll('image')).map(img => img.textContent).filter(img => img)
            };
            
            return {
                success: true,
                car: car,
                data: car
            };
        }
        
        // Generic success/error response (check in response wrapper if present)
        const responseWrapper = xmlDoc.querySelector('response') || xmlDoc.documentElement;
        const successNode = responseWrapper.querySelector('success');
        const messageNode = responseWrapper.querySelector('message');
        const idNode = responseWrapper.querySelector('id');
        
        return {
            success: successNode ? successNode.textContent === 'true' : false,
            message: messageNode ? messageNode.textContent : null,
            id: idNode ? idNode.textContent : null,
            error: errorNode ? errorNode.textContent : null
        };
    } catch (error) {
        console.error('Error parsing XML:', error);
        return null;
    }
}

/**
 * Helper function to safely get text content from XML node
 * @param {Element} parentNode - Parent XML element
 * @param {string} tagName - Child tag name
 * @returns {string} Text content or empty string
 */
function getXMLText(parentNode, tagName) {
    const node = parentNode.querySelector(tagName);
    return node ? node.textContent : '';
}

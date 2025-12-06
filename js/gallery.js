// Image Gallery Functionality for Detail Page

/**
 * Show specific image in gallery
 * @param {number} index - Index of image to show
 */
function showImage(index) {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    galleryItems.forEach((item, i) => {
        if (i === index) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
    
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

/**
 * Initialize gallery keyboard navigation
 * @param {number} totalImages - Total number of images
 */
function initGalleryNavigation(totalImages) {
    if (totalImages <= 1) return;
    
    let currentImageIndex = 0;

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
            showImage(currentImageIndex);
        } else if (e.key === 'ArrowRight') {
            currentImageIndex = (currentImageIndex + 1) % totalImages;
            showImage(currentImageIndex);
        }
    });
}

// Make functions globally available
window.showImage = showImage;
window.initGalleryNavigation = initGalleryNavigation;


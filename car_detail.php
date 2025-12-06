<?php
session_start();
require_once 'config.php';

$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

$carId = isset($_GET['id']) ? $_GET['id'] : '';
$car = $carId ? getCarById($carId) : null;

if (!$car) {
    header('Location: index.php');
    exit;
}

$fromSearch = isset($_GET['from']) && $_GET['from'] === 'search';

if (!empty($car['seller_full_name']) || !empty($car['seller_name'])) {
    $sellerName = $car['seller_full_name'] ?? $car['seller_name'];
    $sellerPicture = $car['seller_picture'] ?? null;
    $isVerified = $car['seller_verified'] ?? false;
    $isDealership = ($car['seller_type'] ?? 'individual') === 'dealership';
    $dealershipName = $car['dealership_name'] ?? null;
    if ($isDealership && $dealershipName) {
        $sellerName = $dealershipName;
    }
} else {
    $sellerName = 'Seller_' . substr($car['id'], -4);
    $sellerPicture = null;
    $isVerified = false;
    $isDealership = false;
}

$isSaved = false;
if ($isLoggedIn) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("SELECT id FROM saved_listings WHERE user_id = ? AND car_id = ?");
        $stmt->execute([$currentUser['id'], $carId]);
        $isSaved = $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Error checking saved listing: " . $e->getMessage());
    }
}

$carUrl = BASE_URL . '/car_detail.php?id=' . urlencode($carId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year'] . ' - $' . number_format($car['price'])); ?>">
    <meta name="car-id" content="<?php echo htmlspecialchars($carId); ?>">
    <meta name="current-user-id" content="<?php echo $isLoggedIn ? $currentUser['id'] : ''; ?>">
    <title><?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']); ?> - <?php echo htmlspecialchars(SITE_NAME); ?></title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/detail.css">
    <link rel="stylesheet" href="css/buttons.css">
    <link rel="stylesheet" href="css/overrides.css">

</head>
<body>

    <div class="detail-container">

        <header class="detail-header">
            <button class="back-btn" id="backButton" onclick="goBack()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <h1>Car Details</h1>
            <div style="width: 40px;"></div> 
        </header>

        <div class="detail-images">
            <?php if (!empty($car['images'])): ?>
                <div class="image-gallery">
                    <?php foreach ($car['images'] as $index => $image): ?>
                            <div class="gallery-item <?php echo $index === 0 ? 'active' : ''; ?>" role="img" aria-label="Image <?php echo $index + 1; ?> of <?php echo count($car['images']); ?>">
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                 alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']); ?> - Image <?php echo $index + 1; ?> of <?php echo count($car['images']); ?>"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f0f0f0\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'20\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($car['images']) > 1): ?>
                    <div class="gallery-thumbnails">
                        <?php foreach ($car['images'] as $index => $image): ?>
                            <button type="button" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="showImage(<?php echo $index; ?>)" aria-label="View image <?php echo $index + 1; ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="Thumbnail <?php echo $index + 1; ?>"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23f0f0f0\' width=\'100\' height=\'100\'/%3E%3C/svg%3E';">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-image-large">
                    <span>No Images Available</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="detail-content">

            <div class="detail-section">
                <div class="seller-info">
                    <?php if ($sellerPicture): ?>
                        <img src="<?php echo htmlspecialchars($sellerPicture); ?>" 
                             alt="<?php echo htmlspecialchars($sellerName); ?>" 
                             class="user-avatar large-img">
                    <?php else: ?>
                        <div class="user-avatar large"><?php echo strtoupper(substr($sellerName, 0, 1)); ?></div>
                    <?php endif; ?>
                    <div class="seller-details">
                        <div class="seller-name">
                            <?php echo htmlspecialchars($sellerName); ?>
                            <?php if ($isVerified): ?>
                                <span class="verified-badge" title="Verified Seller">✓</span>
                            <?php endif; ?>
                            <?php if ($isDealership): ?>
                                <span class="dealership-badge" title="Dealership">🏢</span>
                            <?php endif; ?>
                        </div>
                        <div class="seller-label">
                            <?php echo $isDealership ? 'Dealership' : 'Seller'; ?>
                            <?php if (!empty($car['city']) && !empty($car['country'])): ?>
                                <span style="color: var(--text-secondary);"> • <?php echo htmlspecialchars($car['city'] . ', ' . $car['country']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h2 class="car-title"><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h2>
                <div class="car-price-large">$<?php echo number_format($car['price']); ?></div>
            </div>

            <div class="detail-section">
                <h3 class="section-title">Specifications</h3>
                <div class="specs-grid">
                    <?php if (!empty($car['vehicle_type'])): ?>
                    <div class="spec-item">
                        <div class="spec-label">Vehicle Type</div>
                        <div class="spec-value"><?php echo htmlspecialchars(ucfirst($car['vehicle_type'] === 'motorcycle' ? 'Motorcycle' : $car['vehicle_type'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="spec-item">
                        <div class="spec-label">Make</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['make']); ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Model</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['model']); ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Year</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['year']); ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Mileage</div>
                        <div class="spec-value"><?php echo number_format($car['mileage']); ?> miles</div>
                    </div>
                    <?php if (!empty($car['transmission'])): ?>
                    <div class="spec-item">
                        <div class="spec-label">Transmission</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['transmission']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($car['fuel_type'])): ?>
                    <div class="spec-item">
                        <div class="spec-label">Fuel Type</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['fuel_type']); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="spec-item">
                        <div class="spec-label">Color</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['color']); ?></div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Price</div>
                        <div class="spec-value price-value">$<?php echo number_format($car['price']); ?></div>
                    </div>
                    <?php if (!empty($car['country'])): ?>
                    <div class="spec-item">
                        <div class="spec-label">Country</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['country']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($car['city'])): ?>
                    <div class="spec-item">
                        <div class="spec-label">City</div>
                        <div class="spec-value"><?php echo htmlspecialchars($car['city']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-section">
                <div class="action-buttons">
                    <?php if ($isLoggedIn): ?>
                        <button class="action-btn save-btn-detail <?php echo $isSaved ? 'saved' : ''; ?>" 
                                data-car-id="<?php echo htmlspecialchars($carId); ?>" 
                                aria-label="Save listing"
                                style="background: var(--glass-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 12px; cursor: pointer;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $isSaved ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    <?php endif; ?>
                    <button class="btn-primary btn-full" id="contactSellerBtn">
                        Contact Seller
                    </button>
                    <button class="btn-secondary btn-full" onclick="goBack()">
                        <?php echo $fromSearch ? 'Back to Search' : 'Back to Feed'; ?>
                    </button>
                </div>
            </div>

<section id="comments" data-car-id="<?php echo htmlspecialchars($carId); ?>" style="margin-top:24px;">
  <h3>Comments</h3>

  <div id="comment-form">
    <?php if ($isLoggedIn): ?>

      <textarea id="comment-input" placeholder="Write a comment..." maxlength="1000" rows="3" style="width:100%;padding:8px;border-radius:8px;border:1px solid var(--border-color);"></textarea>

      <div style="display:flex; align-items:center; justify-content:space-between; margin-top:8px;">
        <div id="form-message" class="form-message" style="min-height:18px;"></div>
        <div>
          <span id="char-count">0/1000</span>

          <button id="comment-post-btn" class="btn-primary" style="margin-left:12px;">Post</button>
        </div>
      </div>
    <?php else: ?>
      <p><a href="index.php?force_auth=1">Log in</a> to post a comment.</p>
    <?php endif; ?>
  </div>

  <div id="comments-container" style="margin-top:16px;"></div>
</section>

            <div id="contactModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Contact Seller</h2>
                        <button class="modal-close" onclick="closeContactModal()" aria-label="Close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="contact-options">
                        <?php 

                        $displayEmail = $car['seller_email'] ?? $car['seller_contact_email'] ?? null;

                        $displayPhone = $car['seller_phone'] ?? null;
                        ?>
                        <?php if (!empty($displayEmail)): ?>
                            <a href="mailto:<?php echo htmlspecialchars($displayEmail); ?>" 
                               class="contact-option btn-primary contact-btn-clickable">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span>Email: <?php echo htmlspecialchars($displayEmail); ?></span>
                                <span class="click-hint">Click to email</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($displayPhone)): ?>
                            <a href="tel:+961<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $displayPhone)); ?>" 
                               class="contact-option btn-secondary contact-btn-clickable">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span>Phone: +961 <?php echo htmlspecialchars($displayPhone); ?></span>
                                <span class="click-hint">Click to call</span>
                            </a>
                        <?php endif; ?>
                        <?php if (empty($displayEmail) && empty($displayPhone)): ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                                Contact information not available for this seller.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

 <script>

        const totalImages = <?php echo count($car['images']); ?>;
        if (typeof initGalleryNavigation === 'function') {
            initGalleryNavigation(totalImages);
        }

        function goBack() {
            const fromSearch = <?php echo $fromSearch ? 'true' : 'false'; ?>;

            if (fromSearch) {

                window.location.href = 'search.php?from=detail';
            } else {

                window.location.href = 'index.php';
            }
        }

        window.goBack = goBack;

        document.getElementById('contactSellerBtn')?.addEventListener('click', function() {
            document.getElementById('contactModal').style.display = 'flex';
        });

        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
        }

        document.querySelector('.save-btn-detail')?.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            const isSaved = this.classList.contains('saved');

            fetch('api/save_listing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    car_id: carId,
                    action: isSaved ? 'unsave' : 'save'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'saved') {
                        this.classList.add('saved');
                        this.querySelector('svg').setAttribute('fill', 'currentColor');
                    } else {
                        this.classList.remove('saved');
                        this.querySelector('svg').setAttribute('fill', 'none');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        window.addEventListener('click', function(event) {
            const contactModal = document.getElementById('contactModal');
            if (event.target === contactModal) {
                closeContactModal();
            }
        });
    </script>
<script src="js/gallery.js"></script>
<script src="js/feed-interactions.js"></script>
<script src="js/comments.js" defer></script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>


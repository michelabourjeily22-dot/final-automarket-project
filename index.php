<?php
session_start();
require_once 'config.php';

if (isset($_GET['guest']) && $_GET['guest'] === '1') {
    $_SESSION['guest'] = 1;
}

$forceAuth = isset($_GET['force_auth']) && $_GET['force_auth'] === '1';
if ($forceAuth) {
    unset($_SESSION['guest']);
}

$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

$authError = $_SESSION['auth_error'] ?? '';
$authActiveTab = $_SESSION['auth_active_tab'] ?? 'login';
$authPrefill = $_SESSION['auth_prefill'] ?? ['full_name' => '', 'email' => '', 'username' => ''];

unset($_SESSION['auth_error'], $_SESSION['auth_active_tab'], $_SESSION['auth_prefill']);

$showAuthModal = !$isLoggedIn && ($forceAuth || empty($_SESSION['guest']));

$cars = getAllCars();

$cars = array_reverse($cars);

$dbError = null;
if (empty($cars) && isset($_GET['db_error'])) {
    $dbError = 'Database connection issue. Please check your configuration.';
}

$successMessage = isset($_GET['success']) ? 'Car listing added successfully!' : '';

$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];

$savedListingIds = [];
if ($isLoggedIn) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("SELECT car_id FROM saved_listings WHERE user_id = ?");
        $stmt->execute([$currentUser['id']]);
        $savedListingIds = array_column($stmt->fetchAll(), 'car_id');
    } catch (Exception $e) {
        error_log("Error getting saved listings: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse and search car listings on AutoFeed - Your trusted marketplace for buying and selling vehicles.">
    <title>Car Listings - <?php echo htmlspecialchars(SITE_NAME); ?></title>

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/buttons.css">
    <link rel="stylesheet" href="css/toast.css">
    <link rel="stylesheet" href="css/overrides.css">

</head>
<body>

    <?php if ($successMessage): ?>
        <div class="toast" id="successToast">
            <span><?php echo htmlspecialchars($successMessage); ?></span>
        </div>
    <?php endif; ?>

    <div class="feed-container">

        <header class="feed-header">
            <h1>AutoFeed</h1>
            <div class="header-actions">
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="btn-profile">
                        <?php if (!empty($currentUser['profile_picture'])): ?>

                                 <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" 
                                 alt="Profile" 
                                 class="avatar avatar--sm">
                        <?php else: ?>
                            <div class="profile-avatar-small-text"><?php echo strtoupper(substr($currentUser['full_name'] ?? $currentUser['username'], 0, 1)); ?></div>
                        <?php endif; ?>
                        <span>Profile</span>
                    </a>
                <?php else: ?>
                    <a href="index.php?force_auth=1" class="btn-login">Sign In</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="feed-main" id="main-content" role="main">
            <?php if (empty($cars)): ?>
                <div class="empty-feed">
                    <div class="empty-icon">🚗</div>
                    <h2>No listings yet</h2>
                    <p>Be the first to post a car!</p>
                    <a href="add_post.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Post Your First Car</a>
                </div>
            <?php else: ?>
                <?php foreach ($cars as $car): 
                    $description = $car['year'] . ' • ' . number_format($car['mileage']) . ' miles • ' . $car['color'];

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

                    $isSaved = in_array($car['id'], $savedListingIds);
                    $carUrl = BASE_URL . '/car_detail.php?id=' . urlencode($car['id']);

                    $isOwner = $isLoggedIn && isset($currentUser['id']) && isset($car['user_id']) && $car['user_id'] == $currentUser['id'];

                    $postedDate = '';
                    if (!empty($car['created_at'])) {
                        $date = new DateTime($car['created_at']);
                        $now = new DateTime();
                        $diff = $now->diff($date);

                        if ($diff->days == 0) {
                            $postedDate = 'Today';
                        } elseif ($diff->days == 1) {
                            $postedDate = 'Yesterday';
                        } elseif ($diff->days < 7) {
                            $postedDate = $date->format('l'); 

                        } else {
                            $postedDate = $date->format('M j, Y'); 

                        }
                    }
                ?>
                    <article class="feed-post" data-car-id="<?php echo htmlspecialchars($car['id']); ?>" data-car-image="<?php echo !empty($car['images'][0]) ? htmlspecialchars($car['images'][0]) : ''; ?>">

                        <div class="post-header">
                            <div class="post-user">
                                <?php if ($sellerPicture): ?>
                                    <img src="<?php echo htmlspecialchars($sellerPicture); ?>" 
                                         alt="<?php echo htmlspecialchars($sellerName); ?>" 
                                         class="user-avatar-img">
                                <?php else: ?>
                                    <div class="user-avatar"><?php echo strtoupper(substr($sellerName, 0, 1)); ?></div>
                                <?php endif; ?>
                                <div class="username-container">
                                    <span class="username">
                                        <?php echo htmlspecialchars($sellerName); ?>
                                        <?php if ($isVerified): ?>
                                            <span class="verified-badge" title="Verified Seller">✓</span>
                                        <?php endif; ?>
                                        <?php if ($isDealership): ?>
                                            <span class="dealership-badge" title="Dealership">🏢</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <a href="car_detail.php?id=<?php echo urlencode($car['id']); ?>" class="post-image-link" aria-label="View details for <?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                            <div class="post-image-container">
                                <?php if (!empty($car['images'][0])): ?>
                                    <img src="<?php echo htmlspecialchars($car['images'][0]); ?>" 
                                         alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']); ?>"
                                         class="post-image"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f0f0f0\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'20\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                                <?php else: ?>
                                    <div class="post-image-placeholder" role="img" aria-label="No image available">
                                        <span>No Image</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (count($car['images']) > 1): ?>
                                    <div class="image-indicator">
                                        <span>+<?php echo count($car['images']) - 1; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="post-actions">
                            <?php if ($isLoggedIn): ?>
                                <button class="action-btn save-btn <?php echo $isSaved ? 'saved' : ''; ?>" 
                                        data-car-id="<?php echo htmlspecialchars($car['id']); ?>" 
                                        aria-label="Save listing">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $isSaved ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>
                                <button class="action-btn comment-btn" 
                                        data-car-id="<?php echo htmlspecialchars($car['id']); ?>" 
                                        aria-label="Comment">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </button>
                                <button class="action-btn share-btn" 
                                        data-car-id="<?php echo htmlspecialchars($car['id']); ?>" 
                                        data-car-url="<?php echo htmlspecialchars($carUrl); ?>"
                                        data-car-title="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']); ?>"
                                        aria-label="Share">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <polyline points="16 6 12 2 8 6"></polyline>
                                        <line x1="12" y1="2" x2="12" y2="15"></line>
                                    </svg>
                                </button>
                                <?php if ($isOwner): ?>
                                    <button class="action-btn delete-btn" 
                                            data-car-id="<?php echo htmlspecialchars($car['id']); ?>" 
                                            aria-label="Delete listing"
                                            title="Delete your listing">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="action-btn" onclick="window.location.href='login.php'" aria-label="Sign in to save">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>
                                <button class="action-btn" onclick="window.location.href='login.php'" aria-label="Sign in to comment">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </button>
                                <button class="action-btn share-btn" 
                                        data-car-id="<?php echo htmlspecialchars($car['id']); ?>" 
                                        data-car-url="<?php echo htmlspecialchars($carUrl); ?>"
                                        data-car-title="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' ' . $car['year']); ?>"
                                        aria-label="Share">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <polyline points="16 6 12 2 8 6"></polyline>
                                        <line x1="12" y1="2" x2="12" y2="15"></line>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="post-content">
                            <div class="post-title">
                                <strong><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></strong>
                                <span class="post-price">$<?php echo number_format($car['price']); ?></span>
                            </div>
                            <p class="post-description">
                                <strong><?php echo htmlspecialchars($sellerName); ?></strong> 
                                <?php echo htmlspecialchars($description); ?>
                            </p>
                            <?php if (!empty($car['city'])): ?>
                                <div class="post-location" style="font-size: 13px; color: var(--text-secondary); margin-top: 6px;">
                                    <?php echo htmlspecialchars($car['city']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($postedDate): ?>
                                <div class="post-date" style="font-size: 12px; color: var(--text-tertiary); margin-top: 4px;">
                                    Posted <?php echo htmlspecialchars($postedDate); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <nav class="bottom-nav">
            <button class="nav-item active" onclick="window.location.href='index.php'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Home</span>
            </button>
            <a href="search.php" class="nav-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <span>Search</span>
            </a>
            <a href="add_post.php" class="nav-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Post</span>
            </a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="nav-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Profile</span>
                </a>
            <?php else: ?>
                <a href="index.php?force_auth=1" class="nav-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>Sign In</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <?php if ($showAuthModal): ?>
    <div id="authModal" class="modal active">
        <div class="modal-content auth-modal">
            <div class="modal-header">
                <h2 id="authTitle">Log in</h2>
                <button class="modal-close" onclick="closeAuthModal()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="auth-modal-body">
                <?php if (!empty($authError)): ?>
                    <div class="auth-error">
                        <?php echo htmlspecialchars($authError); ?>
                    </div>
                <?php endif; ?>

                <div id="authLogin" class="auth-section active">
                    <form action="login_handler.php" method="POST" class="auth-form">
                        <label class="auth-field">
                            <span class="auth-label">Email or Username</span>
                            <input type="text" name="identifier" id="loginIdentifier"
                                   placeholder="you@example.com or username"
                                   class="auth-input" required>
                        </label>
                        <label class="auth-field">
                            <span class="auth-label">Password</span>
                            <input type="password" name="password"
                                   placeholder="Enter your password"
                                   class="auth-input" required>
                        </label>
                        <button type="submit" class="btn-primary auth-primary-btn">
                            Log in
                        </button>
                    </form>

                    <button type="button" class="link-button"
                            onclick="openSignupFromLogin()">
                        Don’t have an account? Sign up
                    </button>
                </div>

                <div id="authSignup" class="auth-section">
                    <form action="register_handler.php" method="POST" class="auth-form">
                        <label class="auth-field">
                            <span class="auth-label">Full name</span>
                            <input type="text" name="full_name" id="signupFullName"
                                   placeholder="Your name" class="auth-input" required
                                   value="<?php echo htmlspecialchars($authPrefill['full_name'] ?? ''); ?>">
                        </label>
                        <label class="auth-field">
                            <span class="auth-label">Email address</span>
                            <input type="email" name="email" id="signupEmail"
                                   placeholder="you@example.com" class="auth-input" required
                                   value="<?php echo htmlspecialchars($authPrefill['email'] ?? ''); ?>">
                        </label>
                        <label class="auth-field">
                            <span class="auth-label">Username</span>
                            <input type="text" name="username" id="signupUsername"
                                   placeholder="Choose a username" class="auth-input" required
                                   value="<?php echo htmlspecialchars($authPrefill['username'] ?? ''); ?>">
                        </label>
                        <label class="auth-field">
                            <span class="auth-label">Password</span>
                            <input type="password" name="password"
                                   placeholder="Create a password" class="auth-input" required>
                        </label>
                        <label class="auth-field">
                            <span class="auth-label">Confirm password</span>
                            <input type="password" name="confirm_password"
                                   placeholder="Repeat your password" class="auth-input" required>
                        </label>
                        <button type="submit" class="btn-primary auth-primary-btn">
                            Sign up
                        </button>
                    </form>

                    <button type="button" class="link-button"
                            onclick="openLoginFromSignup()">
                        Already have an account? Log in
                    </button>
                </div>

                <div class="auth-divider">
                    <span></span>
                    <p>OR</p>
                    <span></span>
                </div>

                <a href="<?php echo 'https://accounts.google.com/o/oauth2/v2/auth?client_id=' . urlencode(GOOGLE_CLIENT_ID) . '&redirect_uri=' . urlencode(GOOGLE_REDIRECT_URI) . '&response_type=code&scope=openid%20email%20profile&access_type=online&prompt=select_account'; ?>" 
                   class="btn-primary auth-google-btn">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <button type="button" class="btn-secondary auth-guest-btn" onclick="window.location.href='index.php?guest=1'">
                    Continue as Guest
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div id="searchModal" class="modal">
        <div class="modal-content search-modal">
            <div class="modal-header">
                <h2>Search Cars</h2>
                <button class="modal-close" onclick="closeSearchModal()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="search-container">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search by make, model, year..." autocomplete="off" aria-label="Search cars by make, model, or year">
                </div>
            <div class="search-filters">
                    <div class="multi-select-wrapper">
                        <div class="multi-select-trigger" id="makeTrigger">
                            <span class="multi-select-text">All Makes</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <div class="multi-select-dropdown" id="makeDropdown">
                            <div class="multi-select-option select-all-option" data-value="all">
                                <input type="checkbox" id="makeAll" class="multi-select-checkbox">
                                <label for="makeAll">All Makes</label>
                            </div>
                            <?php 
                            if (!empty($cars)) {
                            $makes = array_unique(array_column($cars, 'make'));
                            sort($makes);
                            foreach ($makes as $make): 
                            ?>
                                <div class="multi-select-option" data-value="<?php echo htmlspecialchars($make); ?>">
                                    <input type="checkbox" id="make_<?php echo htmlspecialchars($make); ?>" class="multi-select-checkbox" value="<?php echo htmlspecialchars($make); ?>">
                                    <label for="make_<?php echo htmlspecialchars($make); ?>"><?php echo htmlspecialchars($make); ?></label>
                                </div>
                            <?php 
                                endforeach;
                            } else {

                                echo '<div class="multi-select-option" style="color: var(--text-tertiary); padding: 12px 16px; font-style: italic;">No makes available. Add listings first.</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="multi-select-wrapper">
                        <div class="multi-select-trigger" id="yearTrigger">
                            <span class="multi-select-text">All Years</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <div class="multi-select-dropdown" id="yearDropdown">
                            <div class="multi-select-option select-all-option" data-value="all">
                                <input type="checkbox" id="yearAll" class="multi-select-checkbox">
                                <label for="yearAll">All Years</label>
                            </div>
                            <?php 
                            if (!empty($cars)) {
                            $years = array_unique(array_column($cars, 'year'));
                            rsort($years);
                            foreach ($years as $year): 
                            ?>
                                <div class="multi-select-option" data-value="<?php echo htmlspecialchars($year); ?>">
                                    <input type="checkbox" id="year_<?php echo htmlspecialchars($year); ?>" class="multi-select-checkbox" value="<?php echo htmlspecialchars($year); ?>">
                                    <label for="year_<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></label>
                                </div>
                            <?php 
                                endforeach;
                            } else {

                                echo '<div class="multi-select-option" style="color: var(--text-tertiary); padding: 12px 16px; font-style: italic;">No years available. Add listings first.</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <input type="number" id="maxPrice" placeholder="Max Price" min="0">
                </div>
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
                </div>

    <div id="shareModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Share Listing</h2>
                <button class="modal-close" onclick="closeShareModal()" aria-label="Close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="share-options">
                <button class="share-option" data-platform="whatsapp">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span>WhatsApp</span>
                </button>
                <button class="share-option" data-platform="facebook">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </button>
                <button class="share-option" data-platform="twitter">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    <span>Twitter/X</span>
                </button>
                <button class="share-option" data-platform="messenger">
                    <svg width="24" height="24" viewBox="0 0 36 36" fill="currentColor">

                        <path
    d="M0 15.521c0-8.922 6.984-15.521 16-15.521s16 6.599 16 15.521c0 8.917-6.984 15.521-16 15.521-1.615 0-3.172-0.214-4.625-0.615-0.286-0.078-0.589-0.052-0.854 0.068l-3.188 1.401c-0.833 0.37-1.776-0.224-1.802-1.135l-0.094-2.854c-0.010-0.349-0.167-0.672-0.422-0.906-3.245-2.927-5.073-7.109-5.016-11.479zM11.094 12.599l-4.693 7.469c-0.469 0.703 0.427 1.521 1.094 1l5.052-3.828c0.349-0.266 0.802-0.266 1.161 0l3.729 2.802c1.125 0.839 2.724 0.531 3.469-0.641l4.693-7.469c0.469-0.703-0.427-1.505-1.094-1l-5.052 3.828c-0.333 0.266-0.802 0.266-1.146 0l-3.734-2.802c-1.125-0.849-2.729-0.552-3.479 0.641z"
  ></path>
                    </svg>
                    <span>Messenger</span>
                </button>
                <button class="share-option" data-platform="copy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Copy Link</span>
                </button>
            </div>
        </div>
    </div>

    <?php if (!empty($errors) || isset($_GET['form_error'])): ?>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            window.location.href = 'add_post.php';
        });
    </script>
    <?php endif; ?>

<div id="profileEditModal" class="modal" style="display:none;">
  <div class="modal-content" style="max-width:720px;">
    <div class="modal-header">
      <h2>Edit Profile</h2>
      <button class="modal-close" onclick="closeProfileModal()" aria-label="Close">✕</button>
    </div>
    <form id="profileEditForm" enctype="multipart/form-data">
      <div style="display:flex; gap:16px; align-items:flex-start;">
        <div style="width:120px; text-align:center;">

          <div id="avatarPreview" class="avatar avatar--md">
          <?php if (!empty($currentUser['profile_picture'])): ?>

              <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" alt="avatar" id="avatarImg" class="avatar-img" alt="avatar">
              <?php else: ?>
              <div id="avatarPlaceholder" style="font-size:28px;"><?php echo strtoupper(substr($currentUser['full_name'] ?? $currentUser['username'],0,1)); ?></div>
            <?php endif; ?>
          </div>
          <input type="file" id="profilePicture" name="profile_picture" accept="image/*">
        </div>
        <div style="flex:1;">
          <label>
            <div style="font-weight:700">Enter your name</div>
            <input type="text" name="full_name" id="fullName" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" required>
          </label>

          <label style="margin-top:8px; display:block;">
            <div style="font-weight:700">Something about you</div>
            <textarea name="about" id="about" rows="3"><?php echo htmlspecialchars($currentUser['about'] ?? ''); ?></textarea>
          </label>

          <div style="display:flex; gap:12px; margin-top:8px;">
            <label style="flex:1;">
              <div style="font-weight:700">Gender</div>
              <select name="gender" id="gender">
                <option value="">Please select your gender</option>
                <option value="male" <?php if (($currentUser['gender'] ?? '')==='male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if (($currentUser['gender'] ?? '')==='female') echo 'selected'; ?>>Female</option>
                <option value="other" <?php if (($currentUser['gender'] ?? '')==='other') echo 'selected'; ?>>Other</option>
              </select>
            </label>

            <label style="flex:1;">
              <div style="font-weight:700">Date of birth</div>
              <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($currentUser['dob'] ?? ''); ?>">
            </label>
          </div>

          <h3 style="margin-top:12px;">Contact information</h3>
          <label>
            <div style="font-weight:700">Phone Number</div>
            <input type="text" name="phone" id="phone" placeholder="+961..." value="<?php echo htmlspecialchars($contactInfo['phone'] ?? $currentUser['phone'] ?? ''); ?>">
          </label>
          <label style="margin-top:8px;">
            <div style="font-weight:700">WhatsApp</div>
            <input type="text" name="whatsapp" id="whatsapp" value="<?php echo htmlspecialchars($contactInfo['whatsapp'] ?? ''); ?>">
          </label>
          <label style="margin-top:8px;">
            <div style="font-weight:700">Email <span style="color:#d9534f;">*</span></div>
            <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($contactInfo['email'] ?? $currentUser['email'] ?? ''); ?>">
            <div id="emailError" style="color:#d9534f;display:none;">Field should not be empty</div>
          </label>
          <div style="margin-top:12px; display:flex; gap:8px;">
            <button type="submit" class="btn-primary">Save</button>
            <button type="button" class="btn-secondary" onclick="closeProfileModal()">Cancel</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

    <?php require_once 'includes/footer.php'; ?>

    <script src="js/utils.js"></script>
    <script src="js/modal.js"></script>
    <script src="js/multiselect.js"></script>
    <script src="js/search.js"></script>
    <script src="js/toast.js"></script>
    <script src="js/main.js"></script>
    <script src="js/feed-interactions.js"></script>
</body>
</html>


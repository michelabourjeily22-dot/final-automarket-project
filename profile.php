<?php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser();
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("
        SELECT c.*, 
               GROUP_CONCAT(ci.image_path ORDER BY ci.display_order SEPARATOR '|||') as image_paths
        FROM cars c
        LEFT JOIN car_images ci ON c.id = ci.car_id
        WHERE c.user_id = ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$currentUser['id']]);
    $userListings = [];
    while ($row = $stmt->fetch()) {
        $userListings[] = [
            'id' => $row['id'],
            'make' => $row['make'],
            'model' => $row['model'],
            'year' => (int)$row['year'],
            'price' => (float)$row['price'],
            'images' => !empty($row['image_paths']) ? explode('|||', $row['image_paths']) : [],
            'created_at' => $row['created_at'] ?? null
        ];
    }

    $stmt = $pdo->prepare("
        SELECT c.*, 
               GROUP_CONCAT(ci.image_path ORDER BY ci.display_order SEPARATOR '|||') as image_paths
        FROM saved_listings sl
        JOIN cars c ON sl.car_id = c.id
        LEFT JOIN car_images ci ON c.id = ci.car_id
        WHERE sl.user_id = ?
        GROUP BY c.id
        ORDER BY sl.created_at DESC
    ");
    $stmt->execute([$currentUser['id']]);
    $savedListings = [];
    while ($row = $stmt->fetch()) {
        $savedListings[] = [
            'id' => $row['id'],
            'make' => $row['make'],
            'model' => $row['model'],
            'year' => (int)$row['year'],
            'price' => (float)$row['price'],
            'images' => !empty($row['image_paths']) ? explode('|||', $row['image_paths']) : [],
            'created_at' => $row['created_at'] ?? null
        ];
    }

    $stmt = $pdo->prepare("SELECT * FROM user_contacts WHERE user_id = ?");
    $stmt->execute([$currentUser['id']]);
    $contactInfo = $stmt->fetch();

} catch (Exception $e) {
    error_log("Profile error: " . $e->getMessage());
    $userListings = [];
    $savedListings = [];
    $contactInfo = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your profile on AutoFeed">
    <title>Profile - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/buttons.css">
    <link rel="stylesheet" href="css/feed.css">
    <link rel="stylesheet" href="css/overrides.css">
    <style>
        .profile-container {
            min-height: 100vh;
            background: var(--bg-primary);
            padding-bottom: 80px;
        }
        .profile-header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
        }
        .profile-header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }
        .profile-avatar-text {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            color: white;
            border: 3px solid var(--primary-color);
        }
        .profile-info h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        .profile-info p {
            margin: 4px 0;
            color: var(--text-secondary);
        }
        .profile-badges {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-verified {
            background: var(--primary-glow);
            color: white;
        }
        .badge-dealership {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        .profile-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        .profile-tabs {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border-color);
        }
        .profile-tab {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all var(--transition-base);
            margin-bottom: -2px;
        }
        .profile-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        .profile-tab-content {
            display: none;
        }
        .profile-tab-content.active {
            display: block;
        }
        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .listing-card {
            background: var(--glass-bg);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all var(--transition-base);
            position: relative;
        }
        .listing-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .listing-card-image-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .listing-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        .listing-delete-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-base);
            z-index: 10;
            opacity: 0;
            transform: scale(0.9);
        }
        .listing-card:hover .listing-delete-btn {
            opacity: 1;
            transform: scale(1);
        }
        .listing-delete-btn:hover {
            background: rgba(255, 68, 68, 0.9);
            border-color: rgba(255, 68, 68, 0.5);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.4);
        }
        .listing-delete-btn:active {
            transform: scale(0.95);
        }
        .listing-delete-btn svg {
            stroke: currentColor;
        }
        .listing-card-content {
            padding: 16px;
        }
        .listing-card-title {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .listing-card-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 18px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <header class="profile-header">
            <div class="profile-header-content">
                <?php if (!empty($currentUser['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" 
                         alt="Profile" 
                         class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar-text">
                        <?php echo strtoupper(substr($currentUser['full_name'] ?? $currentUser['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-info">
                    <h1>
                        <?php echo htmlspecialchars($currentUser['full_name'] ?? $currentUser['username']); ?>
                        <?php if ($currentUser['is_verified']): ?>
                            <span class="badge badge-verified">✓ Verified</span>
                        <?php endif; ?>
                        <?php if ($currentUser['user_type'] === 'dealership'): ?>
                            <span class="badge badge-dealership">🏢 Dealership</span>
                        <?php endif; ?>
                    </h1>
                    <p><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></p>
                    <?php if ($currentUser['user_type'] === 'dealership' && !empty($currentUser['dealership_name'])): ?>
                        <p><strong>Dealership:</strong> <?php echo htmlspecialchars($currentUser['dealership_name']); ?></p>
                    <?php endif; ?>
                    <div class="profile-badges">
                        <span><?php echo count($userListings); ?> Listings</span>
                        <span>•</span>
                        <span><?php echo count($savedListings); ?> Saved</span>
                    </div>
                </div>
                <div style="margin-left: auto;">
                    <a href="logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </header>

        <div class="profile-content">
            <div class="profile-tabs">
                <button class="profile-tab active" data-tab="listings">My Listings</button>
                <button class="profile-tab" data-tab="saved">Saved Listings</button>
                <button class="profile-tab" data-tab="contact">Contact Info</button>
            </div>

            <div class="profile-tab-content active" id="listings">
                <?php if (empty($userListings)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🚗</div>
                        <h2>No listings yet</h2>
                        <p>Start selling by posting your first car!</p>
                        <a href="add_post.php" class="btn-primary" style="margin-top: 16px; text-decoration: none; display: inline-block;">Post a Car</a>
                    </div>
                <?php else: ?>
                    <div class="listings-grid">
                        <?php foreach ($userListings as $listing): ?>
                            <div class="listing-card">
                                <div class="listing-card-image-wrapper">
                                    <a href="car_detail.php?id=<?php echo urlencode($listing['id']); ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <?php if (!empty($listing['images'][0])): ?>
                                            <img src="<?php echo htmlspecialchars($listing['images'][0]); ?>" 
                                                 alt="<?php echo htmlspecialchars($listing['make'] . ' ' . $listing['model']); ?>"
                                                 class="listing-card-image">
                                        <?php else: ?>
                                            <div class="listing-card-image" style="background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; color: var(--text-tertiary);">
                                                No Image
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <button class="listing-delete-btn" 
                                            data-car-id="<?php echo htmlspecialchars($listing['id']); ?>" 
                                            aria-label="Delete listing"
                                            title="Delete listing">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                                <div class="listing-card-content">
                                    <a href="car_detail.php?id=<?php echo urlencode($listing['id']); ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="listing-card-title">
                                            <?php echo htmlspecialchars($listing['make'] . ' ' . $listing['model'] . ' ' . $listing['year']); ?>
                                        </div>
                                        <div class="listing-card-price">
                                            $<?php echo number_format($listing['price']); ?>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-tab-content" id="saved">
                <?php if (empty($savedListings)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">❤️</div>
                        <h2>No saved listings</h2>
                        <p>Save listings you're interested in by clicking the heart icon!</p>
                    </div>
                <?php else: ?>
                    <div class="listings-grid">
                        <?php foreach ($savedListings as $listing): ?>
                            <a href="car_detail.php?id=<?php echo urlencode($listing['id']); ?>" class="listing-card" style="text-decoration: none; color: inherit;">
                                <?php if (!empty($listing['images'][0])): ?>
                                    <img src="<?php echo htmlspecialchars($listing['images'][0]); ?>" 
                                         alt="<?php echo htmlspecialchars($listing['make'] . ' ' . $listing['model']); ?>"
                                         class="listing-card-image">
                                <?php else: ?>
                                    <div class="listing-card-image" style="background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; color: var(--text-tertiary);">
                                        No Image
                                    </div>
                                <?php endif; ?>
                                <div class="listing-card-content">
                                    <div class="listing-card-title">
                                        <?php echo htmlspecialchars($listing['make'] . ' ' . $listing['model'] . ' ' . $listing['year']); ?>
                                    </div>
                                    <div class="listing-card-price">
                                        $<?php echo number_format($listing['price']); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="profile-tab-content" id="contact">
                <div style="background: var(--glass-bg); padding: 24px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h2 style="margin-top: 0;">Contact Information</h2>
                    <?php if ($contactInfo): ?>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($contactInfo['email'] ?? $currentUser['email'] ?? 'Not provided'); ?></p>
                        <?php if (!empty($contactInfo['phone'])): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($contactInfo['phone']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($contactInfo['whatsapp'])): ?>
                            <p><strong>WhatsApp:</strong> 
                                <a href="https://wa.me/<?php echo htmlspecialchars($contactInfo['whatsapp']); ?>" target="_blank" style="color: var(--primary-color);">
                                    <?php echo htmlspecialchars($contactInfo['whatsapp']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Contact information not set. You can add it when posting listings.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Home</span>
        </a>
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
        <a href="profile.php" class="nav-item active">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Profile Edit Modal (paste before includes/footer.php) -->
<div id="profileEditModal" class="modal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div class="modal-backdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.45);" onclick="closeProfileModal()"></div>
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="profileEditTitle"
       style="position:relative; max-width:900px; width:100%; background:var(--glass-bg); border-radius:12px; padding:18px; border:1px solid var(--border-color); box-shadow:var(--shadow-lg);">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
      <h2 id="profileEditTitle" style="margin:0;">Edit Profile</h2>
      <button type="button" onclick="closeProfileModal()" aria-label="Close" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
    </div>

    <form id="profileEditForm" enctype="multipart/form-data" style="display:flex; gap:16px; flex-wrap:wrap;">
      <div style="min-width:120px; text-align:center;">
        <div id="avatarPreview" style="width:100px;height:100px;border-radius:50%;overflow:hidden;margin:0 auto 8px;background:#eee;display:flex;align-items:center;justify-content:center;">
          <?php if (!empty($currentUser['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" alt="avatar" id="avatarImg" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <div id="avatarPlaceholder" style="font-size:28px;"><?php echo strtoupper(substr($currentUser['full_name'] ?? $currentUser['username'],0,1)); ?></div>
          <?php endif; ?>
        </div>
        <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="width:100%;">
      </div>

      <div style="flex:1; min-width:240px;">
        <label style="display:block; margin-bottom:8px;">
          <div style="font-weight:700; margin-bottom:6px;">Enter your name</div>
          <input type="text" name="full_name" id="fullName" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
        </label>

        <label style="display:block; margin-bottom:8px;">
          <div style="font-weight:700; margin-bottom:6px;">Something about you</div>
          <textarea name="about" id="about" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);"><?php echo htmlspecialchars($currentUser['about'] ?? ''); ?></textarea>
        </label>

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:8px;">
          <label style="flex:1; min-width:120px;">
            <div style="font-weight:700; margin-bottom:6px;">Gender</div>
            <select name="gender" id="gender" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
              <option value="">Please select your gender</option>
              <option value="male" <?php if (($currentUser['gender'] ?? '')==='male') echo 'selected'; ?>>Male</option>
              <option value="female" <?php if (($currentUser['gender'] ?? '')==='female') echo 'selected'; ?>>Female</option>
              <option value="other" <?php if (($currentUser['gender'] ?? '')==='other') echo 'selected'; ?>>Other</option>
            </select>
          </label>

          <label style="flex:1; min-width:150px;">
            <div style="font-weight:700; margin-bottom:6px;">Date of birth</div>
            <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($currentUser['dob'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
          </label>
        </div>

        <h3 style="margin:8px 0 6px;">Contact information</h3>

        <label style="display:block; margin-bottom:8px;">
          <div style="font-weight:700; margin-bottom:6px;">Phone Number</div>
          <input type="text" name="phone" id="phone" placeholder="+961..." value="<?php echo htmlspecialchars($contactInfo['phone'] ?? $currentUser['phone'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
        </label>

        <label style="display:block; margin-bottom:8px;">
          <div style="font-weight:700; margin-bottom:6px;">WhatsApp</div>
          <input type="text" name="whatsapp" id="whatsapp" value="<?php echo htmlspecialchars($contactInfo['whatsapp'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
        </label>

        <label style="display:block; margin-bottom:8px;">
          <div style="font-weight:700; margin-bottom:6px;">Email <span style="color:#d9534f;">*</span></div>
          <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($contactInfo['email'] ?? $currentUser['email'] ?? ''); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
          <div id="emailError" style="color:#d9534f;display:none; margin-top:6px;">Field should not be empty</div>
        </label>

        <div style="margin-top:12px; display:flex; gap:8px;">
          <button type="submit" class="btn-primary">Save</button>
          <button type="button" class="btn-secondary" onclick="closeProfileModal()">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

    <script src="js/feed-interactions.js"></script>
    <script>

document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const targetTab = this.getAttribute('data-tab');

        if (targetTab === 'contact') {
            openProfileModal();
            return;
        }

        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        document.querySelectorAll('.profile-tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(targetTab).classList.add('active');
    });
});

function openProfileModal() {
    const modal = document.getElementById('profileEditModal');
    if (!modal) {
        console.warn('profileEditModal not found');
        return;
    }
    modal.style.display = 'flex';

    try {
        const first = modal.querySelector('input, textarea, select, button');
        if (first) first.focus();
    } catch (e) {}
}

function closeProfileModal() {
    const modal = document.getElementById('profileEditModal');
    if (!modal) return;
    modal.style.display = 'none';
}

document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeProfileModal();
});

(function(){
    const profilePicture = document.getElementById('profilePicture');
    if (!profilePicture) return;

    profilePicture.addEventListener('change', function(e){
        const f = this.files && this.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function(ev){
            const imgEl = document.getElementById('avatarImg');
            if (imgEl) {
                imgEl.src = ev.target.result;
                return;
            }
            const placeholder = document.getElementById('avatarPlaceholder');
            if (placeholder) placeholder.style.display = 'none';
            const container = document.getElementById('avatarPreview');
            if (container) container.innerHTML = `<img id="avatarImg" src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(f);
    });
})();

(function(){
    const form = document.getElementById('profileEditForm');
    if (!form) return;

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const emailInput = form.querySelector('#email');
        if (!emailInput) return;
        const email = emailInput.value.trim();
        const emailErrorEl = document.getElementById('emailError');
        if (!email) {
            if (emailErrorEl) emailErrorEl.style.display = 'block';
            emailInput.focus();
            return;
        } else {
            if (emailErrorEl) emailErrorEl.style.display = 'none';
        }

        const fd = new FormData(form);

        fetch('api/update_profile.php', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error saving profile: ' + (data.error || 'Unknown error'));
                return;
            }

            location.reload();
        })
        .catch(() => alert('An error occurred while saving profile.'));
    });
})();

document.getElementById('profilePicture')?.addEventListener('change', function(e){
    const f = this.files && this.files[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = function(ev){
        const imgEl = document.getElementById('avatarImg');
        if (imgEl) {
            imgEl.src = ev.target.result;
            return;
        }
        const placeholder = document.getElementById('avatarPlaceholder');
        if (placeholder) placeholder.style.display = 'none';
        const container = document.getElementById('avatarPreview');
        container.innerHTML = `<img id="avatarImg" src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    };
    reader.readAsDataURL(f);
});

document.getElementById('profileEditForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.currentTarget;
    const email = form.querySelector('#email').value.trim();
    if (!email) {
        document.getElementById('emailError').style.display = 'block';
        return;
    }
    const fd = new FormData(form);
    fetch('api/update_profile.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert('Error saving profile: ' + (data.error || 'Unknown error'));
            return;
        }

        location.reload();
    })
    .catch(() => alert('An error occurred while saving profile.'));
});

        document.querySelectorAll('.listing-delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const carId = this.getAttribute('data-car-id');
                const listingCard = this.closest('.listing-card');

                if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
                    return;
                }

                this.disabled = true;
                this.style.opacity = '0.5';

                fetch('api/delete_listing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        car_id: carId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        if (listingCard) {
                            listingCard.style.transition = 'opacity 0.3s, transform 0.3s';
                            listingCard.style.opacity = '0';
                            listingCard.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                listingCard.remove();

                                const listingsGrid = document.querySelector('.listings-grid');
                                if (listingsGrid && listingsGrid.querySelectorAll('.listing-card').length === 0) {
                                    const tabContent = document.getElementById('listings');
                                    tabContent.innerHTML = `
                                        <div class="empty-state">
                                            <div class="empty-state-icon">🚗</div>
                                            <h2>No listings yet</h2>
                                            <p>Start selling by posting your first car!</p>
                                            <a href="add_post.php" class="btn-primary" style="margin-top: 16px; text-decoration: none; display: inline-block;">Post a Car</a>
                                        </div>
                                    `;
                                }
                            }, 300);
                        }
                    } else {
                        this.disabled = false;
                        this.style.opacity = '1';
                        alert('Error: ' + (data.error || 'Failed to delete listing'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.disabled = false;
                    this.style.opacity = '1';
                    alert('An error occurred. Please try again.');
                });
            });
        });
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>


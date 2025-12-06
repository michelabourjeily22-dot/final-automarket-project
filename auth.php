<?php
session_start();
require_once 'config.php';

// Handle Google OAuth callback
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Exchange authorization code for access token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $tokenData = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $tokenResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        header('Location: login.php?error=' . urlencode('Failed to authenticate with Google'));
        exit;
    }
    
    $tokenData = json_decode($tokenResponse, true);
    
    if (!isset($tokenData['access_token'])) {
        header('Location: login.php?error=' . urlencode('Failed to get access token'));
        exit;
    }
    
    // Get user info from Google
    $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($tokenData['access_token']);
    
    $ch = curl_init($userInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userInfoResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        header('Location: login.php?error=' . urlencode('Failed to get user information'));
        exit;
    }
    
    $userInfo = json_decode($userInfoResponse, true);
    
    if (!isset($userInfo['id'])) {
        header('Location: login.php?error=' . urlencode('Invalid user information'));
        exit;
    }
    
    // Create or update user in database
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Check if user exists by Google UID
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_uid = ?");
        $stmt->execute([$userInfo['id']]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser) {
            // Update existing user
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET google_email = ?, 
                    full_name = ?,
                    profile_picture = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE google_uid = ?
            ");
            $updateStmt->execute([
                $userInfo['email'] ?? null,
                $userInfo['name'] ?? null,
                $userInfo['picture'] ?? null,
                $userInfo['id']
            ]);
            
            $_SESSION['user_id'] = $existingUser['id'];
            $_SESSION['user_name'] = $userInfo['name'] ?? $existingUser['full_name'];
            $_SESSION['user_email'] = $userInfo['email'] ?? $existingUser['email'];
            $_SESSION['user_picture'] = $userInfo['picture'] ?? $existingUser['profile_picture'];
        } else {
            // Create new user
            $insertStmt = $pdo->prepare("
                INSERT INTO users (google_uid, google_email, email, full_name, profile_picture, username, user_type)
                VALUES (?, ?, ?, ?, ?, ?, 'individual')
            ");
            
            // Generate username from email or name
            $username = !empty($userInfo['email']) 
                ? explode('@', $userInfo['email'])[0] 
                : strtolower(str_replace(' ', '', $userInfo['name'] ?? 'user' . time()));
            
            // Ensure username is unique
            $usernameCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $usernameCheck->execute([$username]);
            if ($usernameCheck->fetch()) {
                $username .= time();
            }
            
            $insertStmt->execute([
                $userInfo['id'],
                $userInfo['email'] ?? null,
                $userInfo['email'] ?? null,
                $userInfo['name'] ?? null,
                $userInfo['picture'] ?? null,
                $username
            ]);
            
            $userId = $pdo->lastInsertId();
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userInfo['name'] ?? null;
            $_SESSION['user_email'] = $userInfo['email'] ?? null;
            $_SESSION['user_picture'] = $userInfo['picture'] ?? null;
        }
        
        // Redirect to home
        header('Location: index.php');
        exit;
        
    } catch (Exception $e) {
        error_log("Auth error: " . $e->getMessage());
        header('Location: login.php?error=' . urlencode('Database error. Please try again.'));
        exit;
    }
} else {
    // No code parameter, redirect to login
    header('Location: login.php');
    exit;
}


<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';

if ($identifier === '' || $password === '') {
    $_SESSION['auth_error'] = 'Please enter both email/username and password.';
    $_SESSION['auth_active_tab'] = 'login';
    header('Location: index.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Allow login by email or username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        $_SESSION['auth_error'] = 'Invalid credentials. Please check your details and try again.';
        $_SESSION['auth_active_tab'] = 'login';
        header('Location: index.php');
        exit;
    }

    // Successful login
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
    $_SESSION['user_email'] = $user['email'] ?? null;
    $_SESSION['user_picture'] = $user['profile_picture'] ?? null;

    header('Location: index.php');
    exit;
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['auth_error'] = 'Server error while logging in. Please try again.';
    $_SESSION['auth_active_tab'] = 'login';
    header('Location: index.php');
    exit;
}



<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$_SESSION['auth_prefill'] = [
    'full_name' => $fullName,
    'email' => $email,
    'username' => $username,
];

if ($fullName === '' || $email === '' || $username === '' || $password === '' || $confirmPassword === '') {
    $_SESSION['auth_error'] = 'Please fill in all fields.';
    $_SESSION['auth_active_tab'] = 'signup';
    header('Location: index.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['auth_error'] = 'Please enter a valid email address.';
    $_SESSION['auth_active_tab'] = 'signup';
    header('Location: index.php');
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['auth_error'] = 'Passwords do not match.';
    $_SESSION['auth_active_tab'] = 'signup';
    header('Location: index.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['auth_error'] = 'Password must be at least 6 characters.';
    $_SESSION['auth_active_tab'] = 'signup';
    header('Location: index.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Check for existing email or username
    $stmt = $pdo->prepare("SELECT id, email, username FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt->execute([$email, $username]);
    $existing = $stmt->fetch();

    if ($existing) {
        if (strcasecmp($existing['email'], $email) === 0) {
            $_SESSION['auth_error'] = 'An account with this email already exists.';
        } else {
            $_SESSION['auth_error'] = 'Username is already taken.';
        }
        $_SESSION['auth_active_tab'] = 'signup';
        header('Location: index.php');
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, full_name, user_type)
        VALUES (?, ?, ?, ?, 'individual')
    ");
    $insert->execute([$username, $email, $passwordHash, $fullName]);

    $userId = $pdo->lastInsertId();

    unset($_SESSION['auth_prefill']);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_email'] = $email;

    header('Location: index.php');
    exit;
} catch (Exception $e) {
    error_log('Registration error: ' . $e->getMessage());
    $_SESSION['auth_error'] = 'Server error while creating your account. Please try again.';
    $_SESSION['auth_active_tab'] = 'signup';
    header('Location: index.php');
    exit;
}



<?php
session_start();
require_once 'config.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$continueAsGuest = isset($_GET['guest']) && $_GET['guest'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to AutoFeed with Google or continue as guest">
    <title>Sign In - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/buttons.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            padding: 20px;
        }
        .login-box {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        .login-header p {
            color: var(--text-secondary);
            font-size: 14px;
        }
        .login-options {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .btn-google {
            background: white;
            color: #333;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all var(--transition-base);
        }
        .btn-google:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .btn-google svg {
            width: 20px;
            height: 20px;
        }
        .btn-guest {
            background: rgba(26, 26, 26, 0.6);
            color: var(--text-primary);
            border: 1.5px solid var(--border-color);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 24px;
            color: #ff4444;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🚗 AutoFeed</h1>
                <p>Sign in to like, comment, and share</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="login-options">
                <a href="<?php echo 'https://accounts.google.com/o/oauth2/v2/auth?client_id=' . urlencode(GOOGLE_CLIENT_ID) . '&redirect_uri=' . urlencode(GOOGLE_REDIRECT_URI) . '&response_type=code&scope=openid%20email%20profile&access_type=online'; ?>" 
                   class="btn-primary btn-google">
                    <svg viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign in with Google
                </a>
                
                <a href="index.php?guest=1" class="btn-secondary btn-guest btn-full">
                    Continue as Guest
                </a>
            </div>
            
            <div style="text-align: center; margin-top: 24px; font-size: 12px; color: var(--text-tertiary);">
                <p>By continuing, you agree to our Terms of Service</p>
            </div>
        </div>
    </div>
    
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>


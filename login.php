<?php
session_start();
require_once 'includes/db.php';

if (is_logged_in()) {
    if ($_SESSION['role'] === 'admin') {
        redirect('admin/index.php');
    } else {
        redirect('dashboard.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        $error = 'Please enter both login and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('dashboard.php');
            }
        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            padding: 48px;
            margin: 20px;
        }
    </style>
</head>
<body class="animate-up">
    <div class="glass-card auth-card">
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="index.php" class="logo" style="justify-content: center; margin-bottom: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                DLC<span>SMS</span>
            </a>
            <h2 style="font-size: 1.75rem;">Welcome Back</h2>
            <p class="text-muted">Access your council services</p>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.08); color: var(--error); padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; border: 1px solid rgba(239, 68, 68, 0.1);">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label class="form-label">Username or Email</label>
                <input type="text" name="login" class="form-control" required placeholder="name@example.com">
            </div>
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" style="margin-bottom: 0;">Password</label>
                    <a href="recover.php" style="font-size: 0.8rem; color: var(--secondary); text-decoration: none; font-weight: 600;">Forgot?</a>
                </div>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1.05rem;">Sign In to Console</button>
        </form>

        <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.05);">
            <p class="text-muted" style="font-size: 0.95rem;">New to DLCSMS? <a href="register.php" style="color: var(--secondary); font-weight: 700; text-decoration: none;">Create Account</a></p>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or Email already exists.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'citizen')");
            if ($stmt->execute([$username, $email, $password_hash])) {
                $success = 'Registration successful! You can now <a href="login.php" style="color:inherit; font-weight:700;">Login</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | DLCSMS</title>
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
            max-width: 480px;
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
            <h2 style="font-size: 1.75rem;">Create Environment</h2>
            <p class="text-muted">Initialize your citizen account</p>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.08); color: var(--error); padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; border: 1px solid rgba(239, 68, 68, 0.1);">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: rgba(34, 197, 94, 0.08); color: var(--success); padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 500; border: 1px solid rgba(34, 197, 94, 0.1);">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="johndoe">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="john@email.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Environment Key</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1.05rem;">Initialize Registration</button>
        </form>

        <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.05);">
            <p class="text-muted" style="font-size: 0.95rem;">Already a member? <a href="login.php" style="color: var(--secondary); font-weight: 700; text-decoration: none;">Sign In</a></p>
        </div>
    </div>
</body>
</html>

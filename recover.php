<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        // Find user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            // In a real app, send email with token. Here we just mock it.
            $success = "A password reset link has been sent to <strong>" . h($email) . "</strong>. (Note: Email sending is mocked in this demo)";
        } else {
            $error = "No account found with that email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="animate-fade-in">
    <div class="container" style="max-width: 450px; margin-top: 100px;">
        <div class="glass-card" style="padding: 40px;">
            <div class="text-center mb-4">
                <a href="index.php" class="logo">DLC<span>SMS</span></a>
                <h2 class="mt-4">Recover Password</h2>
                <p class="text-muted">Enter your email to receive a reset link</p>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.1); color: var(--error); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="recover.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="your@email.com">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Link</button>
            </form>

            <div class="text-center mt-4">
                <a href="login.php" style="color: var(--secondary); font-size: 0.9rem; text-decoration: none;">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>

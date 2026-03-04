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
    <title>Recover | DLCSMS</title>
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
    <div style="position: fixed; top: 40px; left: 40px; z-index: 100;">
        <a href="index.php" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 12px; font-weight: 700; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Portal
        </a>
    </div>

    <div class="glass-card auth-card">
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="index.php" class="logo" style="justify-content: center; margin-bottom: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                DLC<span>SMS</span>
            </a>
            <h2 style="font-size: 1.75rem;">Recover Access</h2>
            <p class="text-muted">Enter your email for security validation</p>
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

        <form action="recover.php" method="POST">
            <div class="form-group">
                <label class="form-label">Active Email Handle</label>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 52px; font-size: 1.05rem;">Send Validate Link</button>
        </form>

        <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.05);">
            <p class="text-muted" style="font-size: 0.95rem;"><a href="login.php" style="color: var(--secondary); font-weight: 700; text-decoration: none;">Return to Sign In</a></p>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'includes/db.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = 'Email node cannot be empty.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        if ($stmt->execute([$email, $user_id])) {
            $success = 'Identity telemetry updated successfully.';
            $user['email'] = $email;
        } else {
            $error = 'Synchronization failure.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 56px;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: var(--secondary);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            margin: 0 auto 32px;
            box-shadow: var(--shadow-glow);
        }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include 'includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <div class="glass-card profile-container">
                    <div class="profile-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                    <header style="text-align: center; margin-bottom: 48px;">
                        <h2 style="font-size: 2rem;">Identity Profile</h2>
                        <p class="text-muted">Manage your digital citizen credentials.</p>
                    </header>

                    <?php if($success): ?>
                        <div style="background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 16px; border-radius: 12px; margin-bottom: 24px; text-align: center;"><?= $success ?></div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Username (Immutable)</label>
                            <input type="text" class="form-control" value="<?= h($user['username']) ?>" readonly style="background: #f1f5f9; cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Interface</label>
                            <input type="email" name="email" class="form-control" value="<?= h($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Security Clearance</label>
                            <input type="text" class="form-control" value="<?= strtoupper(h($user['role'])) ?>" readonly style="background: #f1f5f9; cursor: not-allowed;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 56px; margin-top: 24px;">Update Registry</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

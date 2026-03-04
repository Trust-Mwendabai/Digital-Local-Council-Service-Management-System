<?php
session_start();
require_once 'includes/db.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Mark all as read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);

// Fetch notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .notif-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .notif-item {
            padding: 24px;
            margin-bottom: 16px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .notif-icon {
            width: 48px;
            height: 48px;
            background: rgba(0,0,0,0.02);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            flex-shrink: 0;
        }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include 'includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <div class="notif-container">
                    <header style="margin-bottom: 40px;">
                        <h2 style="font-size: 2.5rem;">System Notifications</h2>
                        <p class="text-muted">Review latest updates from the council service registry.</p>
                    </header>

                    <?php if (empty($notifications)): ?>
                        <div class="glass-card" style="padding: 60px; text-align: center;">
                            <p class="text-muted" style="font-size: 1.1rem;">Your communication log is currently empty.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div class="glass-card notif-item" style="<?= $notif['is_read'] ? 'opacity: 0.8;' : 'border-left: 4px solid var(--secondary);' ?>">
                                <div class="notif-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                                        <span><?= date('M d, Y • H:i', strtotime($notif['created_at'])) ?></span>
                                        <div style="display: flex; gap: 8px;">
                                            <span style="font-size: 0.65rem; background: rgba(37, 99, 235, 0.1); color: var(--secondary); padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Email</span>
                                            <span style="font-size: 0.65rem; background: rgba(37, 99, 235, 0.1); color: var(--secondary); padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">SMS</span>
                                            <span style="font-size: 0.65rem; background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">In-App</span>
                                        </div>
                                    </div>
                                    <p style="font-size: 1.05rem; line-height: 1.5; color: var(--primary); font-weight: 500; font-family: 'Inter', sans-serif;"><?= h($notif['message']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

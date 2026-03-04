<?php
// header.php - Modular header component
$role = $_SESSION['role'] ?? 'user';
$portal_name = ($role === 'admin') ? 'Admin Registry' : 'Citizen Dashboard';
?>
<nav class="navbar">
    <div style="font-weight: 700; color: var(--text-muted);"><?= h($portal_name) ?></div>
    <div style="display: flex; gap: 24px; align-items: center;">
        <?php if ($role === 'user'): ?>
            <?php 
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$_SESSION['user_id']]);
            $unread = $stmt->fetchColumn();
            ?>
            <a href="notifications.php" class="notification-bell">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <?php if ($unread > 0): ?><span class="unread-dot"></span><?php endif; ?>
            </a>
        <?php endif; ?>
        
        <div style="text-align: right;">
            <div style="font-weight: 700; color: var(--primary);"><?= h($_SESSION['username']) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;"><?= h($role) ?></div>
        </div>
        <a href="<?= $role === 'admin' ? '../logout.php' : 'logout.php' ?>" class="btn btn-secondary" style="padding: 10px 18px;">Logout</a>
    </div>
</nav>

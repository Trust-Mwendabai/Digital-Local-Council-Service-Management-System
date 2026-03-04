<?php
// sidebar.php - Modular sidebar component with collapsible functionality
$role = $_SESSION['role'] ?? 'user';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="fixed-sidebar" id="sidebar">
    <div class="sidebar-toggle" id="sidebarToggle">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </div>

    <a href="<?= $role === 'admin' ? '../index.php' : 'index.php' ?>" class="logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span>DLC<span>SMS</span></span>
    </a>
    
    <nav class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <a href="index.php" class="sidebar-link <?= $current_page === 'index.php' ? 'active' : '' ?>" data-tooltip="Dashboard">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Dashboard</span>
            </a>
            <a href="manage_applications.php" class="sidebar-link <?= $current_page === 'manage_applications.php' ? 'active' : '' ?>" data-tooltip="Applications">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Applications</span>
            </a>
            <a href="manage_users.php" class="sidebar-link <?= $current_page === 'manage_users.php' ? 'active' : '' ?>" data-tooltip="Citizens">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                <span>Citizens</span>
            </a>
            <a href="activity_logs.php" class="sidebar-link <?= $current_page === 'activity_logs.php' ? 'active' : '' ?>" data-tooltip="Audit Logs">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Audit Logs</span>
            </a>
        <?php else: ?>
            <a href="dashboard.php" class="sidebar-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" data-tooltip="Dashboard">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Dashboard</span>
            </a>
            <a href="apply.php" class="sidebar-link <?= $current_page === 'apply.php' ? 'active' : '' ?>" data-tooltip="New Apply">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"></path></svg>
                <span>New Apply</span>
            </a>
            <a href="notifications.php" class="sidebar-link <?= $current_page === 'notifications.php' ? 'active' : '' ?>" data-tooltip="Alerts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span>Alerts</span>
            </a>
        <?php endif; ?>
    </nav>

    <div style="margin-top: auto; padding: 20px 0;">
        <a href="<?= $role === 'admin' ? '../logout.php' : 'logout.php' ?>" class="sidebar-link" style="color: #fda4af;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    
    // Check for saved state
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    toggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
    });
});
</script>
